<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\TransferInvitation as MailTransferInvitation;
use App\Mail\TransferInvitationConfirmation;
use App\Models\Contact;
use App\Models\Domain;
use App\Models\DomainContact;
use App\Models\Nameserver;
use App\Models\TransferInvitation;
use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

final class TransferInvitationController extends Controller
{
    private EppService $eppService;

    public function __construct(EppService $eppService)
    {
        //  $this->middleware('auth')->except(['accept', 'processAccept']);
        $this->eppService = $eppService;
    }

    /**
     * Show the transfer invitation form
     */
    public function showSendForm(Domain $domain)
    {
        if ($domain->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized: You do not own this domain.');
        }

        return view('admin.domains.transfer_invitation_send', ['domain' => $domain]);
    }

    /**
     * Send transfer invitation to new owner's email
     */
    public function send(Request $request, Domain $domain): RedirectResponse
    {
        if ($domain->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized: You do not own this domain.');
        }

        $request->validate([
            'recipient_email' => ['required', 'email', 'max:255'],
        ]);

        $rateLimitKey = 'transfer-invitation:'.Auth::id();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            return redirect()->back()
                ->with('error', 'Too many transfer invitation requests. Please try again later.');
        }

        try {
            DB::beginTransaction();

            //   $authCode = $domain->generateAuthCode();
            $eppInfo = $this->eppService->getDomainInfo($domain->name);
            $authCode = $eppInfo['authInfo']['pw'];
            $token = Str::random(40);
            $invitation = TransferInvitation::create([
                'domain_id' => $domain->id,
                'sender_id' => Auth::id(),
                'recipient_email' => $request->recipient_email,
                'auth_code' => $authCode,
                'token' => $token,
                'expires_at' => now()->addDays(7), // 7-day expiration
            ]);

            Mail::to($request->recipient_email)->send(new MailTransferInvitation($invitation));
            Mail::to($domain->owner->email)->send(new TransferInvitationConfirmation($invitation));

            RateLimiter::hit($rateLimitKey, 3600);

            Log::info('Transfer invitation sent', [
                'domain' => $domain->name,
                'sender_id' => Auth::id(),
                'recipient_email' => $request->recipient_email,
            ]);

            DB::commit();

            return redirect()->route('admin.domains.index')
                ->with('success', 'Transfer invitation sent to '.$request->recipient_email);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to send transfer invitation: '.$e->getMessage(), [
                'domain' => $domain->name,
                'sender_id' => Auth::id(),
                'recipient_email' => $request->recipient_email,
            ]);

            return redirect()->back()
                ->with('error', 'Failed to send transfer invitation: '.$e->getMessage());
        }
    }

    /**
     * Show transfer acceptance form
     */
    public function accept(string $token)
    {
        $invitation = TransferInvitation::where('token', $token)
            //   ->where('expires_at', '>', now())
            //   ->whereNull('accepted_at')
            ->firstOrFail();
        // get domain associeted with invitation
        $domain = $invitation->domain;
        // Update domain set new owner id amd status befor transfer
        $domain->update([
            'status' => 'pending',
            'owner_id' => Auth::id(),
        ]);
        // update invitation accpted by current user now
        $invitation->update(['accepted_by_id' => Auth::id(), 'accepted_at' => now()]);

        //  dd($domain);
        // Get all contacts for the current user with essential fields
        $contacts = Contact::where('user_id', Auth::id())
            ->select('id', 'contact_id', 'name', 'organization', 'email', 'voice')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.domains.transfer_invitation_accept', ['invitation' => $invitation, 'contacts' => $contacts]);
    }

    /**
     * transfer acceptance
     */
    public function processAccept(Request $request, string $token): RedirectResponse
    {
        $invitation = TransferInvitation::where('token', $token)
            // ->where('expires_at', '>', now())
            // ->whereNull('accepted_at')
            ->firstOrFail();

        // only logged in user can accept transfer
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please log in to accept the transfer.')
                ->with('redirect_to', route('domains.transfer.accept', $token));
        }
        //  dd($request->all());
        $request->validate([
            'registrant_contact_id' => ['required', 'exists:contacts,id'],
            'admin_contact_id' => ['nullable', 'exists:contacts,id'],
            'tech_contact_id' => ['nullable', 'exists:contacts,id'],
            'billing_contact_id' => ['nullable', 'exists:contacts,id'],
            'nameservers' => ['array', 'min:2', 'max:4'],
            'nameservers.*' => ['nullable', 'string', 'regex:/^[a-zA-Z0-9\-\.]+$/'],
        ]);

        try {
            DB::beginTransaction();

            $domain = $invitation->domain;
            $authInfo = $invitation->auth_code;

            // Create domain_contacts records
            $contacts = [
                'registrant' => $request->input('registrant_contact_id'),
                'admin' => $request->input('admin_contact_id') ?? $request->input('registrant_contact_id'),
                'tech' => $request->input('tech_contact_id') ?? $request->input('registrant_contact_id'),
                'billing' => $request->input('billing_contact_id') ?? $request->input('registrant_contact_id'),
            ];

            //  $domain->domainContacts()->delete();
            foreach ($contacts as $type => $contactId) {
                if (in_array($this->eppService->infoContact($contactId), [null, []], true)) {
                    throw new Exception("Contact ID $contactId is not registered with the registry.");
                }
                DomainContact::create([
                    'domain_id' => $domain->id,
                    'contact_id' => $contactId,
                    'type' => $type,
                    'user_id' => Auth::id(),
                ]);
            }

            $nameservers = $request->input('nameservers', []);
            //  $domain->nameservers()->delete();
            foreach ($nameservers as $hostname) {

                if ($hostname !== null) {
                    if (! $this->eppService->checkNameserver($hostname)) {
                        throw new Exception("Nameserver $hostname is not registered with the registry.");
                    }
                    $dnsProvider = explode('.', $hostname)[1] ?? 'unknown';
                    Nameserver::create([
                        'domain_id' => $domain->id,
                        'dns_provider' => $dnsProvider,
                        'hostname' => $hostname,
                        'ipv4_addresses' => null,
                        'ipv6_addresses' => null,
                    ]);
                }
            }

            // Send EPP transfer request
            $isFreeTransfer = str_ends_with(mb_strtolower($domain->name), '.rw');
            $period = 1; // Default 1 year
            $periodForEpp = $period.'y';
            $frame = $this->eppService->transferDomain($domain->name, $authInfo, $periodForEpp);
            $client = $this->eppService->getClient();
            Log::debug('EPP Transfer Request', [
                'domain' => $domain->name,
                'auth_info' => $authInfo,
                'period' => $periodForEpp,
                'request' => $frame->saveXML(),
            ]);
            $response = $client->request($frame);

            if (! $response instanceof \AfriCC\EPP\Frame\Response) {
                throw new Exception('Invalid response from registry');
            }

            $result = $response->results()[0];
            if ($result->code() < 1000 || $result->code() >= 2000) {
                throw new Exception("Registry error (code: {$result->code()}): {$result->message()}");
            }

            $responseData = $response->data();
            Log::debug('EPP Transfer Response', [
                'domain' => $domain->name,
                'response' => $response->saveXML(),
                'responseData' => $responseData,
            ]);
            if (! is_array($responseData)) {
                throw new Exception('Unexpected response data format');
            }

            // Update domain status
            $domain->update([
                'status' => 'active',
                'expires_at' => now()->addYears($period),
            ]);

            // Mark invitation as accepted
            $invitation->update([
                'accepted_at' => now(),
                'accepted_by_id' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Transfer invitation accepted', [
                'domain' => $domain->name,
                'accepted_by_id' => Auth::id(),
                'recipient_email' => $invitation->recipient_email,
            ]);

            return redirect()->route('admin.domains.index')
                ->with('success', 'Domain transfer completed successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to accept transfer invitation: '.$e->getMessage(), [
                'domain' => $domain->name,
                'accepted_by_id' => Auth::id(),
                'recipient_email' => $invitation->recipient_email,
            ]);

            return redirect()->route('domains.transfer.accept', $token)
                ->with('error', 'Failed to complete domain transfer: '.$e->getMessage());
        } finally {
            $this->eppService->disconnect();
        }
    }
}
