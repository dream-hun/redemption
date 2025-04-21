<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RegisterDomainRequest;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Domain;
use App\Models\DomainContact;
use App\Services\Epp\EppService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

final class RegisterDomainController extends Controller
{
    private EppService $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    /**
     * Show the domain registration form
     */
    public function index(): View
    {
        $cartItems = Cart::getContent();
        $cartTotal = Cart::getTotal();
        $contactTypes = ['registrant', 'admin', 'tech', 'billing'];

        // Get all contacts for the current user
        $userContacts = Contact::where('user_id', Auth::id())
            ->select('id', 'uuid', 'contact_id', 'name', 'organization', 'email', 'voice')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get domain contacts to determine types
        $domainContacts = DomainContact::where('user_id', Auth::id())
            ->select('id', 'contact_id', 'type')
            ->get()
            ->groupBy('contact_id');

        // Prepare contacts with their types
        $contactsArray = $userContacts->map(function ($contact) use ($domainContacts) {
            $contactData = $contact->toArray();

            // Add contact type if available from domain contacts
            if (isset($domainContacts[$contact->id])) {
                $contactData['contact_type'] = $domainContacts[$contact->id][0]->type;
            } else {
                $contactData['contact_type'] = null; // No specific type
            }

            return $contactData;
        })->toArray();

        // Group contacts by type for easier access in the view
        $existingContacts = [];
        foreach ($contactTypes as $type) {
            // For each contact type, include all contacts
            // This allows any contact to be used for any role
            $existingContacts[$type] = $contactsArray;
        }

        $countries = Country::all();

        return view('domains.register-domain', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'countries' => $countries,
            'contactTypes' => $contactTypes,
            'existingContacts' => $existingContacts,
        ]);
    }

    /**
     * Register a domain with the selected contacts
     *
     * @throws Throwable
     */
    public function register(RegisterDomainRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Get domain name from cart or request
            $domainName = $request->domain_name;

            // Validate that we have all required contacts
            if (! $request->registrant_contact_id || ! $request->admin_contact_id ||
                ! $request->tech_contact_id || ! $request->billing_contact_id) {
                throw new Exception('All contact types (registrant, admin, tech, and billing) are required');
            }

            // Get contacts from database
            $registrantContact = Contact::find($request->registrant_contact_id);
            $adminContact = Contact::find($request->admin_contact_id);
            $techContact = Contact::find($request->tech_contact_id);
            $billingContact = Contact::find($request->billing_contact_id);

            if (! $registrantContact || ! $adminContact || ! $techContact || ! $billingContact) {
                throw new Exception('One or more contacts not found');
            }

            // Verify that all contacts have EPP contact_ids
            if (empty($registrantContact->contact_id) || empty($adminContact->contact_id) ||
                empty($techContact->contact_id) || empty($billingContact->contact_id)) {
                throw new Exception('One or more contacts do not have valid EPP IDs');
            }

            // Ensure all contacts belong to the current user - log for debugging
            $contactIds = [
                $registrantContact->id,
                $adminContact->id,
                $techContact->id,
                $billingContact->id,
            ];

            $eppContactIds = [
                'registrant' => $registrantContact->contact_id,
                'admin' => $adminContact->contact_id,
                'tech' => $techContact->contact_id,
                'billing' => $billingContact->contact_id,
            ];

            // Log the contacts being used
            \Log::info('Registering domain with contacts', [
                'domain' => $domainName,
                'user_id' => Auth::id(),
                'contact_ids' => $contactIds,
                'epp_contact_ids' => $eppContactIds,
                'registrant_contact' => $registrantContact->toArray(),
                'admin_contact' => $adminContact->toArray(),
                'tech_contact' => $techContact->toArray(),
                'billing_contact' => $billingContact->toArray(),
            ]);

            // Check if all contacts belong to the current user
            $userContacts = Contact::whereIn('id', $contactIds)
                ->where('user_id', Auth::id())
                ->get();

            $userContactIds = $userContacts->pluck('id')->toArray();
            $missingContacts = array_diff($contactIds, $userContactIds);

            if ($missingContacts !== []) {
                \Log::warning('User attempted to use contacts that do not belong to them', [
                    'user_id' => Auth::id(),
                    'missing_contact_ids' => $missingContacts,
                ]);
                throw new Exception('You can only use contacts that belong to your account');
            }

            // Filter out empty nameservers
            $nameservers = array_filter($request->nameservers ?? [], function ($ns): bool {
                return ! in_array(mb_trim($ns), ['', '0'], true);
            });

            // If no nameservers provided, use default ones
            if ($nameservers === []) {
                $nameservers = [
                    'ns1.dns-parking.com',
                    'ns2.dns-parking.com',
                ];
            }

            // Log the nameservers being used
            Log::info('Nameservers for domain registration', [
                'domain' => $domainName,
                'nameservers' => $nameservers,
            ]);

            // Log the domain registration attempt
            Log::info('Attempting to register domain', [
                'domain' => $domainName,
                'user_id' => Auth::id(),
                'registrant_contact_id' => $registrantContact->contact_id,
                'admin_contact_id' => $adminContact->contact_id,
                'tech_contact_id' => $techContact->contact_id,
                'billing_contact_id' => $billingContact->contact_id,
                'nameservers' => $nameservers,
            ]);

            // Create domain in EPP registry
            $period = '1y'; // 1 year registration
            $frame = $this->eppService->createDomain(
                $domainName,
                $period,
                $nameservers,
                $eppContactIds['registrant'],
                $eppContactIds['admin'],
                $eppContactIds['tech'],
                $eppContactIds['billing']
            );

            // Send EPP request and get response
            $response = $this->eppService->getClient()->request($frame);

            // Log the EPP response
            Log::info('EPP domain registration response', [
                'domain' => $domainName,
                'response_code' => $response->code(),
                'response_message' => $response->message(),
                'response_data' => $response->data(),
            ]);

            // Check if the EPP registration was successful
            if ($response->code() !== 1000) {
                DB::rollBack();
                throw new Exception('Domain registration failed: '.$response->message());
            }

            // Create domain in local database
            $domain = Domain::create([
                'uuid' => (string) Str::uuid(),
                'name' => $domainName,
                'owner_id' => Auth::id(),
                'registered_at' => now(),
                'expires_at' => now()->addYear(),
                'status' => 'active',
                'auth_code' => Str::random(12),
                'registration_period' => 1,
                'auto_renew' => $request->auto_renew ?? false,
                'whois_privacy' => $request->whois_privacy ?? false,
            ]);

            // Create domain contacts
            $contactTypes = ['registrant', 'admin', 'tech', 'billing'];
            $contacts = [
                'registrant' => $registrantContact,
                'admin' => $adminContact,
                'tech' => $techContact,
                'billing' => $billingContact,
            ];

            foreach ($contactTypes as $type) {
                DomainContact::create([
                    'domain_id' => $domain->id,
                    'contact_id' => $contacts[$type]->id,
                    'type' => $type,
                    'user_id' => Auth::id(),
                ]);
            }

            // Create nameserver records
            foreach ($nameservers as $hostname) {
                if (! empty($hostname)) {
                    $domain->nameservers()->create([
                        'hostname' => $hostname,
                        'ipv4_addresses' => [],
                        'ipv6_addresses' => [],
                    ]);
                }
            }

            // Remove domain from cart
            foreach (Cart::getContent() as $item) {
                if (mb_strtolower($item->name) === mb_strtolower($domainName)) {
                    Cart::remove($item->id);
                }
            }

            DB::commit();

            // Store EPP response in session for display
            session()->flash('epp_response', [
                'code' => $response->code(),
                'message' => $response->message(),
                'data' => $response->data(),
                'domain' => $domainName,
            ]);

            return redirect()->route('domain.registration.success', $domain->uuid)
                ->with('success', 'Domain registered successfully');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Domain registration failed: '.$e->getMessage(), [
                'user_id' => Auth::id(),
                'domain_data' => $request->except(['_token']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to register domain: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Show registration success page
     */
    public function success(Domain $domain): View
    {
        // Ensure user owns this domain
        if ($domain->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('domains.registration-success', ['domain' => $domain]);
    }
}
