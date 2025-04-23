<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DomainTransferRequest;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Domain;
use App\Models\DomainTransfer;
use App\Services\DomainService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

final class TransferDomainController extends Controller
{
    private DomainService $domainService;

    public function __construct(DomainService $domainService)
    {
        $this->domainService = $domainService;
    }

    /**
     * Show the transfer form for a specific domain (both approaches).
     */
    public function index(string $uuid): View|RedirectResponse
    {
        $domain = Domain::where('uuid', $uuid)->firstOrFail();

        // Restrict non-admins to their own domains
        if (! Auth::user()->is_admin && $domain->owner_id !== Auth::id()) {
            Log::warning('Unauthorized access to transfer form', [
                'domain' => $domain->name,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('admin.domains.index')
                ->with('error', 'You do not have permission to transfer this domain.');
        }

        $contacts = Contact::where('user_id', Auth::id())
            ->select('id', 'contact_id', 'name', 'organization', 'email', 'voice')
            ->orderBy('created_at', 'desc')
            ->get();

        // Admins can select from all contacts
        if (Auth::user()->is_admin) {
            $contacts = Contact::select('id', 'contact_id', 'name', 'organization', 'email', 'voice')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $countries = Country::all();

        try {
            $eppInfo = $this->domainService->getEppClient()->getDomainInfo($domain->name);
            Log::debug('Registry response for domain info', [
                'domain' => $domain->name,
                'response' => $eppInfo,
            ]);

            if ($eppInfo && isset($eppInfo['infData'])) {
                $eppInfo = $eppInfo['infData'];
            }
        } catch (Exception $e) {
            Log::warning('EPP service warning - continuing with transfer process', [
                'domain' => $domain->name,
                'error' => $e->getMessage(),
            ]);
            $eppInfo = [];
        }

        return view('admin.domains.transfer', [
            'domain' => $domain,
            'contacts' => $contacts,
            'countries' => $countries,
            'eppInfo' => $eppInfo,
        ]);
    }

    /**
     * Process the domain transfer (registrant change).
     */
    public function transfer(DomainTransferRequest $request, string $uuid): RedirectResponse
    {
        $domain = Domain::where('uuid', $uuid)->firstOrFail();

        // Restrict non-admins to their own domains
        if (! Auth::user()->is_admin && $domain->owner_id !== Auth::id()) {
            Log::error('Unauthorized transfer attempt', [
                'domain' => $domain->name,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('admin.domains.index')
                ->with('error', 'You are not allowed to transfer this domain.');
        }

        try {
            DB::beginTransaction();

            // Create a pending DomainTransfer record
            $transferRecord = DomainTransfer::create([
                'domain_id' => $domain->id,
                'user_id' => Auth::id(),
                'new_registrant_id' => $request->validated()['new_registrant_id'],
                'status' => 'pending',
                'message' => 'Transfer initiated.',
            ]);

            Log::info('DomainTransfer record created', [
                'transfer_id' => $transferRecord->id,
                'domain_id' => $domain->id,
                'user_id' => Auth::id(),
                'new_registrant_id' => $request->validated()['new_registrant_id'],
            ]);

            $result = $this->domainService->changeRegistrant($domain, $request->validated());

            if ($result['success']) {
                $transferRecord->update([
                    'status' => 'completed',
                    'message' => $result['message'],
                ]);

                Log::info('DomainTransfer completed', [
                    'transfer_id' => $transferRecord->id,
                    'domain_id' => $domain->id,
                ]);

                DB::commit();

                return redirect()->route('admin.domains.index')
                    ->with('success', $result['message']);
            }

            $transferRecord->update([
                'status' => 'failed',
                'message' => $result['message'],
            ]);

            Log::warning('DomainTransfer failed', [
                'transfer_id' => $transferRecord->id,
                'domain_id' => $domain->id,
                'message' => $result['message'],
            ]);

            DB::rollBack();

            return redirect()->back()
                ->with('error', $result['message']);
        } catch (Exception $e) {
            DB::rollBack();

            // Update the transfer record if it exists
            if (isset($transferRecord)) {
                $transferRecord->update([
                    'status' => 'failed',
                    'message' => 'Failed to transfer domain: '.$e->getMessage(),
                ]);
            }

            Log::error('Domain transfer failed', [
                'domain' => $domain->name,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to transfer domain: '.$e->getMessage());
        } finally {
            $this->domainService->getEppClient()->disconnect();
        }
    }

    /**
     * Get the auth code for a domain.
     */
    public function getAuthCode(Request $request): RedirectResponse
    {
        $uuid = $request->input('uuid');
        if (! $uuid) {
            return redirect()->back()->with('error', 'No domain selected.');
        }

        $domain = Domain::where('uuid', $uuid)->firstOrFail();

        // Restrict non-admins to their own domains
        if (! Auth::user()->is_admin && $domain->owner_id !== Auth::id()) {
            return redirect()->route('admin.domains.index')
                ->with('error', 'You are not authorized to access this domain.');
        }

        try {
            $result = $this->domainService->getAuthCode($domain);

            if ($result['success']) {
                session()->flash('success', 'Auth code retrieved: '.$result['auth_code']);
                session()->flash('auth_code', $result['auth_code']);

                return redirect()->back();
            }

            $message = $result['code'] === 2303
                ? 'Domain not managed by this registrar. Please obtain the auth code from the current registrar.'
                : $result['message'];

            return redirect()->back()->with('error', $message);
        } catch (Exception $e) {
            Log::error('Failed to retrieve auth code', [
                'domain' => $domain->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to retrieve auth code: '.$e->getMessage());
        }
    }

    /**
     * Show the transfers page (Approach 2).
     */
    public function listTransfers(): View
    {
        // Admins see all domains, non-admins see only their own
        $domains = Auth::user()->is_admin
            ? Domain::all()
            : Domain::where('owner_id', Auth::id())->get();

        // Transfer history for the current user
        $transfers = DomainTransfer::where('user_id', Auth::id())->latest()->get();

        // Admins can optionally see all transfer history
        if (Auth::user()->is_admin) {
            $transfers = DomainTransfer::latest()->get();
        }

        return view('admin.domains.transfers', [
            'domains' => $domains,
            'transfers' => $transfers,
        ]);
    }
}
