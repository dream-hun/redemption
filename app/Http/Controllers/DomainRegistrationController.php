<?php

namespace App\Http\Controllers;

use AfriCC\EPP\Frame\Response;
use App\Http\Requests\Admin\CreateContactRequest;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Domain;
use App\Services\Epp\EppService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class DomainRegistrationController extends Controller
{
    protected EppService $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    public function create()
    {
        $countries = Country::all();
        $cartItems = Cart::getContent();
        $total = Cart::getTotal();

        return view('domains.create-contact', compact('countries', 'cartItems', 'total'));
    }

    public function success(string $domain)
    {
        return view('domains.registration-success', compact('domain'));
    }

    public function registerDomains(CreateContactRequest $request)
    {

        try {
            DB::beginTransaction();

            // Get cart items
            $cartItems = Cart::where('user_id', Auth::id())->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
            }

            // Create contact IDs for registrant, admin, and tech
            $contactIds = [
                'registrant' => 'REG-'.Str::random(8),
                'admin' => 'ADM-'.Str::random(8),
                'tech' => 'TECH-'.Str::random(8),
            ];

            // Create contacts with EPP
            $contactInfo = $request->contact_info;
            $contacts = [];
            $savedContacts = [];

            foreach ($contactIds as $type => $id) {
                // Check if a similar contact already exists for this user and update or create as needed
                $contact = Contact::updateOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'contact_type' => $type,
                        'name' => $contactInfo['name'],
                        'organization' => $contactInfo['organization'],
                        'email' => $contactInfo['email'],
                    ],
                    [
                        'contact_id' => isset($existingContact) ? $existingContact->contact_id : ($type.'-'.Str::random(8)),
                        'street1' => $contactInfo['streets'][0] ?? '',
                        'street2' => $contactInfo['streets'][1] ?? '',
                        'city' => $contactInfo['city'],
                        'province' => $contactInfo['province'],
                        'postal_code' => $contactInfo['postal_code'],
                        'country_code' => $contactInfo['country_code'],
                        'voice' => $contactInfo['voice'],
                        'auth_info' => $result['auth'] ?? '',
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
                        'results' => array_map(function ($result) {
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
                    'domain' => '',
                    'type' => $type,
                    'contact_id' => $contactId,
                ]);

                $contacts[$type] = [
                    'id' => $contactId,
                    'auth_info' => $result['auth'],
                ];

                $savedContacts[$type] = $contact;
            }

            // Register each domain
            $registeredDomains = [];
            foreach ($cartItems as $item) {
                // First check if domain is available
                $availability = $this->eppService->checkDomain([$item->domain]);

                if (empty($availability) || ! isset($availability[$item->domain]) || ! $availability[$item->domain]->available) {
                    $reason = isset($availability[$item->domain]) ? $availability[$item->domain]->reason : 'Domain not available';
                    throw new Exception("Domain {$item->domain} is not available for registration. Reason: {$reason}");
                }

                // Get nameservers from config
                $nameservers = config('app.default_nameservers', [
                    'ns1.ricta.org.rw',
                    'ns2.ricta.org.rw',
                ]);

                // Create domain with EPP
                $frame = $this->eppService->createDomain(
                    $item->domain,
                    (string) $item->period.'y', // Period in years
                    [], // Empty hostAttr array since we're using hostObj
                    $contacts['registrant']['id'],
                    $contacts['admin']['id'],
                    $contacts['tech']['id']
                );

                // Add nameservers as hostObj
                foreach ($nameservers as $ns) {
                    $frame->addHostObj($ns);
                }

                Log::info('EPP Domain Registration Frame:', [
                    'domain' => $item->domain,
                    'period' => $item->period.'y',
                    'nameservers' => $nameservers,
                    'frame' => $frame,
                    'contacts' => [
                        'registrant' => $contacts['registrant']['id'],
                        'admin' => $contacts['admin']['id'],
                        'tech' => $contacts['tech']['id'],
                    ],
                ]);

                $response = $this->eppService->getClient()->request($frame);

                if (! $response || ! $response->success()) {
                    $errorDetails = [];
                    if ($response) {
                        $results = $response->results();
                        if (! empty($results)) {
                            $result = $results[0];
                            $errorDetails = [
                                'code' => $result->code(),
                                'message' => $result->message(),
                            ];
                        }
                    }

                    Log::error('EPP Domain Registration Failed:', [
                        'domain' => $item->domain,
                        'error' => $errorDetails,
                        'period' => $item->period.'y',
                        'reason' => $response && ! empty($results) ? $result->message() : 'No response',
                    ]);

                    throw new Exception("Failed to register domain: {$item->domain}. ".
                        ($response && ! empty($results) ? "Error {$result->code()}: {$result->message()}" : 'No response from EPP server'));
                }

                // Create domain record in database
                $domain = Domain::create([
                    'name' => $item->domain,
                    'owner_id' => Auth::id(),
                    'registrar' => config('app.name'),
                    'status' => 'active',
                    'registered_at' => now(),
                    'expires_at' => now()->addYears($item->period),
                    'registration_period' => $item->period,
                    'auth_code' => Str::random(16),
                    'nameservers' => $nameservers,
                    'whois_privacy' => true,
                ]);

                // Associate contacts with the domain
                $domain->update([
                    'registrant_contact_id' => $savedContacts['registrant']->id,
                    'admin_contact_id' => $savedContacts['admin']->id,
                    'tech_contact_id' => $savedContacts['tech']->id,
                ]);

                $registeredDomains[] = $domain;
            }

            // Clear the cart
            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            return redirect()->route('domains.index')
                ->with('success', 'Domains registered successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Domain registration failed: '.$e->getMessage(), [
                'user_id' => Auth::id(),
                'cart_items' => $cartItems ?? [],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to register domains. Please try again or contact support.');
        } finally {
            $this->eppService->disconnect();
        }
    }

    public function destroy(Domain $domain)
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

    public function renew(Domain $domain, Request $request)
    {
        $request->validate([
            'period' => 'required|integer|min:1|max:10',
        ]);

        try {
            DB::beginTransaction();

            // Check if the domain belongs to the current user
            if ($domain->owner_id !== Auth::id()) {
                return redirect()->back()
                    ->with('error', 'You do not have permission to renew this domain.');
            }

            try {
                // Get current domain info from registry
                $domainInfo = $this->eppService->getDomainInfo($domain->name);

                if (! isset($domainInfo['infData']['exDate'])) {
                    throw new Exception('Could not determine domain expiry date from registry');
                }

                // Parse the registry expiry date
                $registryExpiryDate = \Carbon\Carbon::parse($domainInfo['infData']['exDate']);

                Log::info('Retrieved domain info from registry', [
                    'domain' => $domain->name,
                    'registry_expiry' => $registryExpiryDate->format('Y-m-d'),
                    'local_expiry' => $domain->expires_at,
                ]);

                // Create EPP frame for domain renewal using registry expiry date
                $frame = $this->eppService->renewDomain(
                    $domain->name,
                    $registryExpiryDate,
                    $request->period.'y'
                );

                // Log the request before sending
                Log::info('Sending domain renewal request', [
                    'domain' => $domain->name,
                    'period' => $request->period.'y',
                    'registry_expiry' => $registryExpiryDate->format('Y-m-d'),
                ]);

                $client = $this->eppService->getClient();
                $response = $client->request($frame);

                if (! ($response instanceof Response)) {
                    throw new Exception('Invalid response received from registry');
                }

                // Get the result details
                $result = $response->results()[0];
                if (! $result) {
                    throw new Exception('No result in registry response');
                }

                // Log raw response for debugging
                Log::debug('EPP response received', [
                    'domain' => $domain->name,
                    'code' => $result->code(),
                    'message' => $result->message(),
                    'data' => $response->data(),
                ]);

                // Check if the response indicates success (1000-series codes are success)
                if ($result->code() < 1000 || $result->code() >= 2000) {
                    throw new Exception("Registry error (code: {$result->code()}): {$result->message()}");
                }

                // Calculate new expiry date based on registry's expiry date
                $newExpiryDate = $registryExpiryDate->copy()->addYears($request->period);

                // Log successful response
                Log::info('Domain renewal successful', [
                    'domain' => $domain->name,
                    'old_expiry' => $registryExpiryDate->format('Y-m-d'),
                    'new_expiry' => $newExpiryDate->format('Y-m-d'),
                    'response_data' => $response->data(),
                ]);

                // Update domain expiry in our database
                $domain->update([
                    'expires_at' => $newExpiryDate,
                    'last_renewal_at' => now(),
                ]);

                DB::commit();

                return redirect()->route('admin.domains.index', $domain)
                    ->with('success', 'Domain renewed successfully!');

            } catch (Exception $e) {
                Log::error('Domain renewal failed in registry', [
                    'domain' => $domain->name,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                throw new Exception('Failed to renew domain in registry: '.$e->getMessage());
            }

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Domain renewal failed: '.$e->getMessage(), [
                'domain' => $domain->name,
                'period' => $request->period,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to renew domain. Please try again or contact support. Error: '.$e->getMessage());
        } finally {
            $this->eppService->disconnect();
        }
    }

    public function updateContacts(Domain $domain, $type, Request $request)
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
                    'results' => array_map(function ($result) {
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
                    'results' => array_map(function ($result) {
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

            // Update nameservers in a single operation
            $frame = $this->eppService->updateDomain(
                $domain->name,
                [], // No admin contacts to update
                [], // No tech contacts to update
                $newNameservers, // Add new nameservers
                [], // No host attributes
                [], // No statuses to update
                $oldNameservers // Remove old nameservers
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
