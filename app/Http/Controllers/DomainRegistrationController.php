<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use AfriCC\EPP\Frame\Response;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Domain;
use App\Services\Epp\EppService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use DateTimeImmutable;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

final class DomainRegistrationController extends Controller
{
    private EppService $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    /**
     * Show the contact creation form for domain registration
     */
    public function create(): View
    {
        $countries = Country::all();
        $cartItems = Cart::getContent();
        $total = Cart::getTotal();

        // Get existing contacts for the current user
        $existingContacts = Contact::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('contact_type');

        return view('domains.create-contact', ['countries' => $countries, 'cartItems' => $cartItems, 'total' => $total, 'existingContacts' => $existingContacts]);
    }

    /**
     * Show the registration success page
     */
    public function success(string $domain): View
    {
        return view('domains.registration-success', ['domain' => $domain]);
    }

    /**
     * Register domains with selected or new contacts
     */
    public function registerDomains(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Get cart items
            $cartItems = Cart::getContent();

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
            }

            // Validate based on whether using existing contacts or creating new ones
            if ($request->has('use_existing_contacts') && $request->boolean('use_existing_contacts')) {
                // Custom validation messages for contact IDs
                $messages = [
                    'registrant_contact_id.required' => 'Please select a registrant contact.',
                    'admin_contact_id.required' => 'Please select an administrative contact.',
                    'tech_contact_id.required' => 'Please select a technical contact.',
                    'billing_contact_id.required' => 'Please select a billing contact.',
                    'exists' => 'The selected contact does not exist.',
                ];

                // Validate all contact IDs are present and valid
                $validator = Validator::make($request->all(), [
                    'registrant_contact_id' => 'required|exists:contacts,id',
                    'admin_contact_id' => 'required|exists:contacts,id',
                    'tech_contact_id' => 'required|exists:contacts,id',
                    'billing_contact_id' => 'required|exists:contacts,id',
                ], $messages);

                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput()
                        ->with('error', 'Please select all required contacts.');
                }
            } else {
                // Validate new contact information
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'organization' => 'nullable|string|max:255',
                    'street1' => 'required|string|max:255',
                    'street2' => 'nullable|string|max:255',
                    'city' => 'required|string|max:255',
                    'province' => 'required|string|max:255',
                    'postal_code' => 'required|string|max:255',
                    'country_code' => 'required|string|size:2',
                    'voice' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput()
                        ->with('error', 'Please fill in all required fields for the new contact.');
                }
            }

            $contacts = [];
            $savedContacts = [];

            // Check if using existing contacts or creating new ones
            if ($request->boolean('use_existing_contacts')) {
                // Use existing contacts
                $contactTypes = ['registrant', 'admin', 'tech', 'billing'];

                foreach ($contactTypes as $type) {
                    $contactId = $request->input("{$type}_contact_id");

                    if (! $contactId) {
                        return redirect()->back()
                            ->with('error', "Please select an existing {$type} contact or create a new one.")
                            ->withInput();
                    }

                    $contact = Contact::where('id', $contactId)
                        ->where('user_id', auth()->id())
                        ->first();

                    if (! $contact) {
                        return redirect()->back()
                            ->with('error', "The selected {$type} contact was not found.")
                            ->withInput();
                    }

                    // Prepare contact data for EPP using PHP 8.4 array unpacking
                    $contactData = [
                        'id' => $contact->contact_id,
                        'name' => $contact->name,
                        'organization' => $contact->organization ?? '',
                        'streets' => array_filter([$contact->street1 ?? '', $contact->street2 ?? '']),
                        'city' => $contact->city,
                        'province' => $contact->province,
                        'postal_code' => $contact->postal_code,
                        'country_code' => $contact->country_code,
                        'voice' => $contact->voice,
                        'email' => $contact->email,
                        'fax' => ['number' => '', 'ext' => ''],
                        'disclose' => [],
                    ];

                    // Log the contact data being processed
                    Log::info("Processing {$type} contact for domain registration", [
                        'contact_id' => $contact->contact_id,
                        'contact_data' => array_merge($contactData, ['auth_info' => '[REDACTED]']),
                    ]);

                    // We'll skip trying to create contacts that already exist in the EPP registry
                    // This avoids the 'Object exists (Code: 2302)' error
                    // Instead, we'll just use the existing contact ID for domain registration

                    // Update local contact record if needed
                    if (empty($contact->auth_info)) {
                        // Generate a random auth info if not present
                        $contact->update([
                            'auth_info' => Str::random(16),
                        ]);

                        Log::info("Updated {$type} contact auth info", [
                            'contact_id' => $contact->contact_id,
                        ]);
                    }

                    Log::info("Using existing {$type} contact for domain registration", [
                        'contact_id' => $contact->contact_id,
                    ]);

                    $contacts[$type] = [
                        'id' => $contact->contact_id,
                        'auth_info' => $contact->auth_info,
                    ];

                    $savedContacts[$type] = $contact;
                }
            } else {
                // Create new contacts
                // Generate unique contact IDs for each type
                $contactIds = [
                    'registrant' => 'REG-'.Str::random(8),
                    'admin' => 'ADM-'.Str::random(8),
                    'tech' => 'TECH-'.Str::random(8),
                    'billing' => 'BILL-'.Str::random(8),
                ];

                // Prepare contact information for EPP using PHP 8.4 array unpacking
                $contactInfo = [
                    'name' => $request->name,
                    'organization' => $request->organization,
                    'street1' => $request->street1,
                    'street2' => $request->street2,
                    'city' => $request->city,
                    'province' => $request->province,
                    'postal_code' => $request->postal_code,
                    'country_code' => $request->country_code,
                    'voice' => $request->voice,
                    'email' => $request->email,
                    'streets' => array_filter([$request->street1, $request->street2]),
                ];

                foreach ($contactIds as $type => $id) {
                    // Check if a similar contact already exists for this user and update or create as needed
                    $contact = Contact::updateOrCreate(
                        [
                            'user_id' => auth()->id(),
                            'contact_type' => $type,
                            'name' => $contactInfo['name'],
                            'organization' => $contactInfo['organization'],
                            'email' => $contactInfo['email'],
                        ],
                        [
                            'contact_id' => $id,
                            'street1' => $contactInfo['street1'] ?? '',
                            'street2' => $contactInfo['street2'] ?? '',
                            'city' => $contactInfo['city'],
                            'province' => $contactInfo['province'],
                            'postal_code' => $contactInfo['postal_code'],
                            'country_code' => $contactInfo['country_code'],
                            'voice' => $contactInfo['voice'],
                            'auth_info' => '',  // Will be updated after EPP response
                            'disclose' => [],
                        ]
                    );

                    $contactId = $contact->contact_id;
                    $contactData = array_merge($contactInfo, [
                        'id' => $contactId,
                        'fax' => ['number' => '', 'ext' => ''],
                        'disclose' => [],
                    ]);

                    // Always use createContacts since it handles both creation and updates in EPP
                    Log::info('Sending contact operation to EPP', [
                        'domain' => '',
                        'type' => $type,
                        'operation' => $contact->wasRecentlyCreated ? 'create' : 'update',
                        'contact_id' => $contactId,
                        'contact_data' => array_merge($contactData, ['auth_info' => '[REDACTED]']),
                    ]);

                    $result = $this->eppService->createContacts($contactData);

                    // Log the EPP frame before sending
                    Log::info('EPP Contact Operation Frame', [
                        'domain' => '',
                        'type' => $type,
                        'operation' => $contact->wasRecentlyCreated ? 'create' : 'update',
                        'frame' => (string) $result['frame'],
                    ]);

                    $response = $this->eppService->getClient()->request($result['frame']);

                    // Log the EPP response
                    if ($response) {
                        Log::info('EPP Contact Operation Response', [
                            'domain' => '',
                            'type' => $type,
                            'operation' => $contact->wasRecentlyCreated ? 'create' : 'update',
                            'success' => $response->success(),
                            'results' => array_map(fn ($result): array => [
                                'code' => $result->code(),
                                'message' => $result->message(),
                            ], $response->results()),
                            'data' => $response->data(),
                        ]);
                    }

                    if (! $response || ! $response->success()) {
                        throw new Exception('Failed to '.($contact->wasRecentlyCreated ? 'create' : 'update')." {$type} contact");
                    }

                    Log::info('Successfully '.($contact->wasRecentlyCreated ? 'created' : 'updated').' contact in EPP', [
                        'domain' => '',
                        'type' => $type,
                        'contact_id' => $contactId,
                    ]);

                    // Update contact with auth info from EPP response
                    $contact->update([
                        'auth_info' => $result['auth'] ?? '',
                    ]);

                    $contacts[$type] = [
                        'id' => $contactId,
                        'auth_info' => $result['auth'] ?? '',
                    ];

                    $savedContacts[$type] = $contact;
                }
            }

            // Process domains (register or renew)
            $processedDomains = [];
            foreach ($cartItems as $item) {
                // Extract domain from attributes or use item ID as fallback
                $domainName = isset($item->attributes->domain) ? $item->attributes->domain : $item->id;

                // Remove 'renew_' prefix if present for renewal items
                if (mb_strpos($domainName, 'renew_') === 0) {
                    $domainName = mb_substr($domainName, 6); // Remove the 'renew_' prefix
                }

                // Determine if this is a renewal or registration
                // Check both the item type attribute and the item ID prefix for renewal
                $isRenewal = (isset($item->attributes->type) && $item->attributes->type === 'renewal') ||
                            (mb_strpos($item->id, 'renew_') === 0);
                $operationType = $isRenewal ? 'renewal' : 'registration';

                // Ensure we have a valid domain name
                if (empty($domainName)) {
                    throw new Exception('Invalid domain name in cart item');
                }

                // Set default period if not specified
                $period = isset($item->quantity) ? $item->quantity : 1;

                // Log the operation being performed
                Log::info("Processing domain {$operationType}", [
                    'domain' => $domainName,
                    'period' => $period,
                    'is_renewal' => $isRenewal,
                ]);

                // For registration, check if domain is available
                // For renewal, we skip this check since the domain should already exist
                if (! $isRenewal) {
                    // Only perform domain availability check for new registrations
                    Log::info('Checking domain availability for registration', ['domain' => $domainName]);

                    $availability = $this->eppService->checkDomain([$domainName]);

                    $domainAvailable = $availability !== [] &&
                        isset($availability[$domainName]) &&
                        $availability[$domainName]->available;

                    if (! $domainAvailable) {
                        $reason = isset($availability[$domainName]) ? $availability[$domainName]->reason : 'Domain not available';
                        throw new Exception("Domain {$domainName} is not available for registration. Reason: {$reason}");
                    }
                } else {
                    // For renewals, verify the domain exists in our database
                    Log::info('Skipping availability check for renewal', ['domain' => $domainName]);

                    $existingDomain = Domain::where('name', $domainName)
                        ->where('owner_id', Auth::id())
                        ->first();

                    if (! $existingDomain) {
                        throw new Exception("Domain {$domainName} not found in your account for renewal.");
                    }
                }

                // Get nameservers from config
                $nameservers = config('app.default_nameservers', [
                    'ns1.ricta.org.rw',
                    'ns2.ricta.org.rw',
                ]);

                // Log contact IDs before domain creation
                Log::info("Contact IDs for domain {$operationType}", [
                    'domain' => $domainName,
                    'registrant' => $contacts['registrant']['id'],
                    'admin' => $contacts['admin']['id'],
                    'tech' => $contacts['tech']['id'],
                    'billing' => $contacts['billing']['id'] ?? $contacts['registrant']['id'],
                    'operation_type' => $operationType,
                ]);

                // Create or renew domain with EPP based on operation type
                if ($isRenewal) {
                    try {
                        // For renewal, use the renewDomain method
                        // First, get the domain info to get the expiration date
                        Log::info('Retrieving domain info for renewal', ['domain' => $domainName]);

                        try {
                            $domainInfo = $this->eppService->getDomainInfo($domainName);

                            if ($domainInfo === [] || ! isset($domainInfo['exDate'])) {
                                throw new Exception("Could not retrieve expiration date for domain {$domainName}");
                            }

                            Log::info('Successfully retrieved domain info for renewal', [
                                'domain' => $domainName,
                                'expiry_date' => $domainInfo['exDate'],
                            ]);
                        } catch (Exception $e) {
                            Log::error('Failed to retrieve domain info for renewal', [
                                'domain' => $domainName,
                                'error' => $e->getMessage(),
                            ]);
                            throw new Exception("Could not retrieve information for domain {$domainName}: {$e->getMessage()}", $e->getCode(), $e);
                        }

                        // Parse the expiration date
                        try {
                            $expiryDate = new DateTimeImmutable($domainInfo['exDate']);
                            Log::info('Parsed expiry date for renewal', [
                                'domain' => $domainName,
                                'expiry_date' => $expiryDate->format('Y-m-d'),
                            ]);
                        } catch (Exception $e) {
                            Log::error('Failed to parse expiry date for renewal', [
                                'domain' => $domainName,
                                'expiry_date_raw' => $domainInfo['exDate'],
                                'error' => $e->getMessage(),
                            ]);
                            throw new Exception("Invalid expiration date format for domain {$domainName}: {$e->getMessage()}", $e->getCode(), $e);
                        }

                        // Renew the domain
                        $frame = $this->eppService->renewDomain(
                            $domainName,
                            $expiryDate->format('Y-m-d'),
                            $period.'y' // Period in years
                        );

                        Log::info("Renewal frame created for domain {$domainName}", [
                            'expiry_date' => $domainInfo['exDate'],
                            'period' => $period.'y',
                        ]);
                    } catch (Exception $e) {
                        Log::error('Error preparing domain renewal', [
                            'domain' => $domainName,
                            'error' => $e->getMessage(),
                        ]);
                        throw new Exception("Failed to prepare renewal for domain {$domainName}: {$e->getMessage()}", $e->getCode(), $e);
                    }
                } else {
                    // For registration, use the createDomain method
                    $frame = $this->eppService->createDomain(
                        $domainName,
                        $period.'y', // Period in years
                        [], // Empty hostAttr array since we're using hostObj
                        $contacts['registrant']['id'],
                        $contacts['admin']['id'],
                        $contacts['tech']['id'],
                        $contacts['billing']['id'] ?? $contacts['registrant']['id'] // Use registrant as billing if not specified
                    );
                }

                // Add nameservers as hostObj for registration only
                if (! $isRenewal) {
                    foreach ($nameservers as $ns) {
                        $frame->addHostObj($ns);
                    }
                }

                // Log the EPP frame before sending
                $logData = [
                    'domain' => $domainName,
                    'period' => $period.'y',
                ];

                // Add nameservers and contacts data based on operation type
                if ($isRenewal) {
                    $logData['nameservers'] = [];
                    $logData['contacts'] = [];
                } else {
                    $logData['nameservers'] = $nameservers;

                    // Determine billing contact ID
                    $billingContactId = $contacts['registrant']['id']; // Default to registrant
                    if (isset($contacts['billing']['id'])) {
                        $billingContactId = $contacts['billing']['id'];
                    }

                    $logData['contacts'] = [
                        'registrant' => $contacts['registrant']['id'],
                        'admin' => $contacts['admin']['id'],
                        'tech' => $contacts['tech']['id'],
                        'billing' => $billingContactId,
                    ];
                }

                Log::info("EPP Domain {$operationType} Frame", $logData);

                $response = $this->eppService->getClient()->request($frame);

                if (! $response || ! $response->success()) {
                    $errorDetails = [];
                    $errorMessage = 'No response from EPP server';

                    if ($response) {
                        $results = $response->results();
                        if (! empty($results)) {
                            $result = $results[0];
                            $errorDetails = [
                                'code' => $result->code(),
                                'message' => $result->message(),
                            ];
                            $errorMessage = "Error {$result->code()}: {$result->message()}";
                        }
                    }

                    Log::error("EPP Domain {$operationType} Failed:", [
                        'domain' => $domainName,
                        'error' => $errorDetails,
                        'period' => $period.'y',
                    ]);

                    $operationVerb = $isRenewal ? 'renew' : 'register';
                    throw new Exception("Failed to {$operationVerb} domain: {$domainName}. {$errorMessage}");
                }

                if ($isRenewal) {
                    // Update existing domain record for renewal
                    $domain = Domain::where('name', $domainName)
                        ->where('owner_id', Auth::id())
                        ->first();

                    if (! $domain) {
                        throw new Exception("Domain {$domainName} not found in database for renewal");
                    }

                    // Update the expiration date
                    $domain->update([
                        'expires_at' => $domain->expires_at->addYears($period),
                        'status' => 'active',
                    ]);
                } else {
                    // Create new domain record for registration
                    $domain = Domain::create([
                        'name' => $domainName,
                        'owner_id' => Auth::id(),
                        'registrar' => config('app.name'),
                        'status' => 'active',
                        'registered_at' => now(),
                        'expires_at' => now()->addYears($period),
                        'registration_period' => $period,
                        'auth_code' => Str::random(16),
                        'nameservers' => $nameservers,
                        'whois_privacy' => true,
                    ]);
                }

                // Associate contacts with the domain for new registrations
                if (! $isRenewal) {
                    $domain->update([
                        'registrant_contact_id' => $savedContacts['registrant']->id,
                        'admin_contact_id' => $savedContacts['admin']->id,
                        'tech_contact_id' => $savedContacts['tech']->id,
                        'billing_contact_id' => isset($savedContacts['billing']) ? $savedContacts['billing']->id : null,
                    ]);
                }

                // Log successful domain operation
                Log::info("Domain {$operationType} successful", [
                    'domain' => $domainName,
                    'user_id' => Auth::id(),
                    'expires_at' => $domain->expires_at,
                    'operation' => $operationType,
                ]);

                $processedDomains[] = $domain;
            }

            // Clear the cart
            Cart::clear();

            DB::commit();

            // Determine success message based on operations performed
            $registrationCount = count(array_filter($cartItems, function ($item): bool {
                return ! isset($item->attributes->type) || $item->attributes->type !== 'renewal';
            }));
            $renewalCount = count($cartItems) - $registrationCount;

            $successMessage = '';
            if ($registrationCount > 0 && $renewalCount > 0) {
                $successMessage = "{$registrationCount} domain(s) registered and {$renewalCount} domain(s) renewed successfully!";
            } elseif ($registrationCount > 0) {
                $successMessage = $registrationCount > 1 ? 'Domains registered successfully!' : 'Domain registered successfully!';
            } elseif ($renewalCount > 1) {
                $successMessage = 'Domains renewed successfully!';
            } else {
                $successMessage = 'Domain renewed successfully!';
            }

            return redirect()->route('domains.index')
                ->with('success', $successMessage);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Domain operation failed', [
                'user_id' => Auth::id(),
                'cart_items' => json_encode($cartItems ?? []),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to process domains: '.$e->getMessage());
        } catch (Throwable $t) {
            DB::rollBack();
            Log::critical('Unexpected error during domain registration', [
                'user_id' => Auth::id(),
                'error' => $t->getMessage(),
                'trace' => $t->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'An unexpected error occurred. Please try again or contact support.');
        } finally {
            $this->eppService->disconnect();
        }
    }

    /**
     * Delete a domain
     */
    public function destroy(Domain $domain): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Check if the domain belongs to the current user
            if ($domain->owner_id !== Auth::id()) {
                return redirect()->back()
                    ->with('error', 'You do not have permission to delete this domain.');
            }

            // Create EPP frame for domain deletion
            $frame = $this->eppService->deleteDomain($domain->name);
            $response = $this->eppService->getClient()->request($frame);

            if (! $response || ! $response->success()) {
                throw new Exception('Failed to delete domain from registry');
            }

            // Delete the domain from our database
            $domain->delete();

            DB::commit();

            return redirect()->route('domains.index')
                ->with('success', 'Domain deleted successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Domain deletion failed: '.$e->getMessage(), [
                'domain' => $domain->name,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete domain. Please try again or contact support.');
        } finally {
            $this->eppService->disconnect();
        }
    }

    public function updateContacts(Domain $domain, string $type, Request $request)
    {
        // Log the start of contact update
        Log::info('Starting contact update', [
            'domain' => $domain->name,
            'type' => $type,
            'user_id' => Auth::id(),
        ]);

        // Validate contact type
        if (! in_array($type, ['registrant', 'admin', 'tech'])) {
            Log::warning('Invalid contact type attempted', [
                'domain' => $domain->name,
                'type' => $type,
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Invalid contact type.');
        }

        $request->validate([
            'contact.name' => 'required|string',
            'contact.organization' => 'required|string',
            'contact.streets' => 'required|array',
            'contact.city' => 'required|string',
            'contact.province' => 'required|string',
            'contact.postal_code' => 'required|string',
            'contact.country_code' => 'required|string|size:2',
            'contact.voice' => 'required|string',
            'contact.email' => 'required|email',
        ]);

        try {
            DB::beginTransaction();

            // Check if the domain belongs to the current user
            if ($domain->owner_id !== Auth::id()) {
                Log::warning('Unauthorized contact update attempt', [
                    'domain' => $domain->name,
                    'type' => $type,
                    'user_id' => Auth::id(),
                    'owner_id' => $domain->owner_id,
                ]);

                return redirect()->back()
                    ->with('error', 'You do not have permission to update this domain.');
            }

            // Check if a similar contact already exists for this user and update or create as needed
            $contact = Contact::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'contact_type' => $type,
                    'name' => $request->contact['name'],
                    'organization' => $request->contact['organization'],
                    'email' => $request->contact['email'],
                ],
                [
                    'contact_id' => isset($existingContact) ? $existingContact->contact_id : ($type.'-'.Str::random(8)),
                    'street1' => $request->contact['streets'][0] ?? '',
                    'street2' => $request->contact['streets'][1] ?? '',
                    'city' => $request->contact['city'],
                    'province' => $request->contact['province'],
                    'postal_code' => $request->contact['postal_code'],
                    'country_code' => $request->contact['country_code'],
                    'voice' => $request->contact['voice'],
                    'auth_info' => $result['auth'] ?? '',
                    'disclose' => [],
                ]
            );

            $contactId = $contact->contact_id;
            $contactData = array_merge($request->contact, [
                'id' => $contactId,
                'fax' => ['number' => '', 'ext' => ''],
                'disclose' => [],
            ]);

            // Always use createContacts since it handles both creation and updates in EPP
            Log::info('Sending contact operation to EPP', [
                'domain' => $domain->name,
                'type' => $type,
                'operation' => $contact->wasRecentlyCreated ? 'create' : 'update',
                'contact_id' => $contactId,
                'contact_data' => array_merge($contactData, ['auth_info' => '[REDACTED]']),
            ]);

            $result = $this->eppService->createContacts($contactData);

            // Log the EPP frame before sending
            Log::info('EPP Contact Operation Frame', [
                'domain' => $domain->name,
                'type' => $type,
                'operation' => $contact->wasRecentlyCreated ? 'create' : 'update',
                'frame' => (string) $result['frame'],
            ]);

            $response = $this->eppService->getClient()->request($result['frame']);

            // Log the EPP response
            if ($response) {
                Log::info('EPP Contact Operation Response', [
                    'domain' => $domain->name,
                    'type' => $type,
                    'operation' => $contact->wasRecentlyCreated ? 'create' : 'update',
                    'success' => $response->success(),
                    'results' => array_map(function ($result): array {
                        return [
                            'code' => $result->code(),
                            'message' => $result->message(),
                        ];
                    }, $response->results()),
                    'data' => $response->data(),
                ]);
            }

            if (! $response || ! $response->success()) {
                throw new Exception('Failed to '.($contact->wasRecentlyCreated ? 'create' : 'update')." {$type} contact");
            }

            Log::info('Successfully '.($contact->wasRecentlyCreated ? 'created' : 'updated').' contact in EPP', [
                'domain' => $domain->name,
                'type' => $type,
                'contact_id' => $contactId,
            ]);

            // Update domain contact in EPP
            $adminContacts = [];
            $techContacts = [];

            if ($type === 'admin') {
                $adminContacts = [$contactId];
            } elseif ($type === 'tech') {
                $techContacts = [$contactId];
            }

            Log::info('Updating domain contacts in EPP', [
                'domain' => $domain->name,
                'type' => $type,
                'admin_contacts' => $adminContacts,
                'tech_contacts' => $techContacts,
            ]);

            $frame = $this->eppService->updateDomain(
                $domain->name,
                $adminContacts,
                $techContacts,
                [], // No host objects
                [], // No host attributes
                [], // No statuses
                []  // No host attributes to remove
            );

            // Log the EPP frame before sending
            Log::info('EPP Domain Update Frame', [
                'domain' => $domain->name,
                'type' => $type,
                'frame' => (string) $frame['frame'],
            ]);

            $response = $this->eppService->getClient()->request($frame['frame']);

            // Log the EPP response
            if ($response) {
                Log::info('EPP Domain Update Response', [
                    'domain' => $domain->name,
                    'type' => $type,
                    'success' => $response->success(),
                    'results' => array_map(function ($result): array {
                        return [
                            'code' => $result->code(),
                            'message' => $result->message(),
                        ];
                    }, $response->results()),
                    'data' => $response->data(),
                ]);
            }

            if (! $response || ! $response->success()) {
                throw new Exception('Failed to update domain contact in registry');
            }

            Log::info('Successfully updated domain contacts in EPP', [
                'domain' => $domain->name,
                'type' => $type,
            ]);

            // Update domain contact in database
            $contactField = "{$type}_contact_id";
            $domain->$contactField = $contact->id;
            $domain->save();

            Log::info('Successfully updated domain contact in database', [
                'domain' => $domain->name,
                'type' => $type,
                'contact_field' => $contactField,
                'contact_id' => $contact->id,
            ]);

            DB::commit();

            Log::info('Contact update completed successfully', [
                'domain' => $domain->name,
                'type' => $type,
                'contact_id' => $contactId,
                'database_id' => $contact->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->with('success', ucfirst($type).' contact updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Domain contact update failed: '.$e->getMessage(), [
                'domain' => $domain->name,
                'type' => $type,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update domain contact. Please try again or contact support.');
        } catch (Throwable $e) {
            $e->getMessage();
        } finally {
            $this->eppService->disconnect();
        }

        return null;
    }

    public function editNameservers(Domain $domain)
    {
        $domain = Domain::where('owner_id', Auth::id())->firstOrFail();

        return view('client.domains.edit-nameservers', ['domain' => $domain]);
    }

    public function updateNameservers(Domain $domain, Request $request)
    {
        $request->validate([
            'nameservers' => 'required|array|min:2|max:13',
            'nameservers.*' => 'required|string|regex:/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/',
        ]);

        try {
            DB::beginTransaction();

            // Check if the domain belongs to the current user
            if ($domain->owner_id !== Auth::id()) {
                return redirect()->back()
                    ->with('error', 'You do not have permission to update this domain.');
            }

            // Log the nameserver update attempt
            Log::info('Attempting to update nameservers', [
                'domain' => $domain->name,
                'current_nameservers' => $domain->nameservers,
                'new_nameservers' => $request->nameservers,
            ]);

            // Clean and prepare nameserver arrays
            $newNameservers = array_values(array_filter($request->nameservers));
            $oldNameservers = array_values(array_filter($domain->nameservers ?? []));

            // Determine which nameservers to add and which to remove
            $nameserversToAdd = array_values(array_diff($newNameservers, $oldNameservers));
            $nameserversToRemove = array_values(array_diff($oldNameservers, $newNameservers));

            Log::info('Nameserver changes', [
                'domain' => $domain->name,
                'to_add' => $nameserversToAdd,
                'to_remove' => $nameserversToRemove,
            ]);

            // Update nameservers in a single operation
            $frame = $this->eppService->updateDomain(
                $domain->name,
                [], // No admin contacts to update
                [], // No tech contacts to update
                $nameserversToAdd, // Only add new nameservers that weren't there before
                [], // No host attributes
                [], // No statuses to update
                $nameserversToRemove // Only remove nameservers that are no longer in the list
            );

            // Send the update request
            $response = $this->eppService->getClient()->request($frame['frame']);

            if (! ($response instanceof Response)) {
                throw new Exception('Invalid response received from registry');
            }

            // Get the result details
            $result = $response->results()[0];
            if (! $result) {
                throw new Exception('No result in registry response');
            }

            // Log raw response for debugging
            Log::debug('EPP nameserver update response', [
                'domain' => $domain->name,
                'code' => $result->code(),
                'message' => $result->message(),
                'data' => $response->data(),
            ]);

            // Check if the response indicates success (1000-series codes are success)
            if ($result->code() < 1000 || $result->code() >= 2000) {
                throw new Exception(sprintf('Registry error (code: %d): %s',
                    $result->code(),
                    $result->message()
                ));
            }

            // If successful, update nameservers in database
            $domain->update([
                'nameservers' => $newNameservers,
            ]);

            DB::commit();

            Log::info('Nameservers updated successfully', [
                'domain' => $domain->name,
                'nameservers' => $newNameservers,
            ]);

            return redirect()->route('admin.domains.index')
                ->with('success', 'Domain nameservers updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Domain nameserver update failed: '.$e->getMessage(), [
                'domain' => $domain->name,
                'nameservers' => $request->nameservers,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update domain nameservers: '.$e->getMessage());
        } catch (Throwable $e) {
            return $e->getMessage();
        } finally {
            $this->eppService->disconnect();
        }
    }
}
