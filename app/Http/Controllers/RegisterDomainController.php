<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateContactRequest;
use App\Http\Requests\RegisterDomainRequest;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Domain;
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

class RegisterDomainController extends Controller
{
    protected EppService $eppService;

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
        $existingContacts = Contact::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('contact_type');
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
     * Create a new contact
     *
     * @return RedirectResponse
     *
     * @throws Throwable
     */
    public function createContact(CreateContactRequest $request)
    {
        try {
            DB::beginTransaction();

            // Generate a unique contact ID for EPP
            $contactId = Contact::generateContactIds($request->contact_type);

            // Create contact in local database
            $contact = Contact::create([
                'contact_id' => $contactId,
                'contact_type' => $request->contact_type,
                'name' => $request->name,
                'organization' => $request->organization,
                'email' => $request->email,
                'voice' => $request->voice,
                'street1' => $request->street1,
                'street2' => $request->street2,
                'city' => $request->city,
                'province' => $request->province,
                'postal_code' => $request->postal_code,
                'country_code' => $request->country_code ?? 'RW',
                'user_id' => Auth::id(),
            ]);

            // Prepare contact data for EPP
            $eppContactData = [
                'id' => $contactId,
                'name' => $request->name,
                'organization' => $request->organization ?: '',
                'streets' => [$request->street1 ?? '', $request->street2 ?? ''],
                'city' => $request->city ?? '',
                'province' => $request->province ?? '',
                'postal_code' => $request->postal_code ?? '',
                'country_code' => $request->country_code ?? 'RW',
                'voice' => $request->voice,
                'fax' => [
                    'number' => $request->fax_number ?? '',
                    'ext' => $request->fax_ext ?? '',
                ],
                'email' => $request->email,
                'disclose' => [],
            ];

            // Create contact in EPP registry
            $eppContact = $this->eppService->createContacts($eppContactData);
            $response = $this->eppService->getClient()->request($eppContact['frame']);

            // Update local contact with auth info from EPP
            $contact->update([
                'auth_info' => $eppContact['auth'],
                'epp_status' => 'active',
            ]);

            DB::commit();

            // Format the contact for response
            $formattedContact = [
                'id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
                'voice' => $contact->voice,
                'organization' => $contact->organization,
            ];

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contact created successfully',
                    'contact' => $formattedContact,
                ]);
            }

            return back()->with('success', 'Contact created successfully');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Contact creation failed: '.$e->getMessage(), [
                'user_id' => Auth::id(),
                'contact_data' => $request->except(['_token']),
                'error' => $e->getMessage(),
            ]);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create contact: '.$e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'Failed to create contact: '.$e->getMessage())->withInput();
        }
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

            // Get contacts from database
            $registrantContact = Contact::find($request->registrant_contact_id);
            $adminContact = Contact::find($request->admin_contact_id);
            $techContact = Contact::find($request->tech_contact_id);
            $billingContact = Contact::find($request->billing_contact_id);

            if (! $registrantContact || ! $adminContact || ! $techContact || ! $billingContact) {
                throw new Exception('One or more contacts not found');
            }

            // Filter out empty nameservers
            $nameservers = array_filter($request->nameservers ?? [], function ($ns) {
                return ! empty($ns);
            });

            // If no nameservers provided, use default ones
            if (empty($nameservers)) {
                $nameservers = [
                    'ns1.example.com',
                    'ns2.example.com',
                ];
            }

            // Prepare nameserver data for EPP
            $hostAttrs = [];
            foreach ($nameservers as $ns) {
                $hostAttrs[$ns] = [];
            }

            // Create domain in EPP registry
            $period = '1'; // 1 year registration
            $frame = $this->eppService->createDomain(
                $domainName,
                $period,
                $hostAttrs,
                $registrantContact->contact_id,
                $adminContact->contact_id,
                $techContact->contact_id,
                $billingContact->contact_id
            );

            $response = $this->eppService->getClient()->request($frame);

            // Create domain in local database
            $domain = Domain::create([
                'name' => $domainName,
                'user_id' => Auth::id(),
                'registrant_contact_id' => $registrantContact->id,
                'admin_contact_id' => $adminContact->id,
                'tech_contact_id' => $techContact->id,
                'billing_contact_id' => $billingContact->id,
                'registration_date' => now(),
                'expiration_date' => now()->addYear(),
                'status' => 'active',
                'nameservers' => json_encode($nameservers),
                'auth_info' => Str::random(12),
            ]);

            // Remove domain from cart
            foreach (Cart::getContent() as $item) {
                if (strtolower($item->name) === strtolower($domainName)) {
                    Cart::remove($item->id);
                }
            }

            DB::commit();

            return redirect()->route('domain.registration.success', $domain->id)
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
