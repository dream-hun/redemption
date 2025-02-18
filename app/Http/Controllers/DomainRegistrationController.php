<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Domain;
use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DomainRegistrationController extends Controller
{
    protected $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    public function create()
    {
        $countries = Country::all();
        $cartItems = Cart::where('user_id', Auth::id())->get();

        return view('domains.create-contact', compact('countries', 'cartItems'));
    }

    public function registerDomains(Request $request)
    {
        // Validate request
        $request->validate([
            'contact_info' => 'required|array',
            'contact_info.name' => 'required|string',
            'contact_info.organization' => 'required|string',
            'contact_info.streets' => 'required|array',
            'contact_info.city' => 'required|string',
            'contact_info.province' => 'required|string',
            'contact_info.postal_code' => 'required|string',
            'contact_info.country_code' => 'required|string|size:2',
            'contact_info.voice' => 'required|string',
            'contact_info.email' => 'required|email',
        ]);

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
                $contactData = array_merge($contactInfo, [
                    'id' => $id,
                    'fax' => ['number' => '', 'ext' => ''],
                    'disclose' => [],
                ]);

                $result = $this->eppService->createContacts($contactData);
                $response = $this->eppService->getClient()->request($result['frame']);

                if (! $response || ! $response->success()) {
                    throw new Exception("Failed to create {$type} contact");
                }

                $contacts[$type] = [
                    'id' => $id,
                    'auth_info' => $result['auth'],
                ];

                // Save contact to database
                $savedContacts[$type] = Contact::create([
                    'contact_id' => $id,
                    'name' => $contactInfo['name'],
                    'organization' => $contactInfo['organization'],
                    'street1' => $contactInfo['streets'][0] ?? '',
                    'street2' => $contactInfo['streets'][1] ?? '',
                    'city' => $contactInfo['city'],
                    'province' => $contactInfo['province'],
                    'postal_code' => $contactInfo['postal_code'],
                    'country_code' => $contactInfo['country_code'],
                    'voice' => $contactInfo['voice'],
                    'email' => $contactInfo['email'],
                    'auth_info' => $result['auth'],
                    'disclose' => [],
                    'user_id' => Auth::id(),
                ]);
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

            // Create EPP frame for domain renewal
            $frame = $this->eppService->renewDomain(
                $domain->name,
                $domain->expires_at->format('Y-m-d'),
                $request->period.'y'
            );

            $response = $this->eppService->getClient()->request($frame);

            if (! $response || ! $response->success()) {
                throw new Exception('Failed to renew domain in registry');
            }

            // Update domain expiry in our database
            $domain->update([
                'expires_at' => $domain->expires_at->addYears($request->period),
                'last_renewal_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('domains.show', $domain)
                ->with('success', 'Domain renewed successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Domain renewal failed: '.$e->getMessage(), [
                'domain' => $domain->name,
                'period' => $request->period,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to renew domain. Please try again or contact support.');
        } finally {
            $this->eppService->disconnect();
        }
    }

    public function updateContacts(Domain $domain, Request $request)
    {
        $request->validate([
            'contact_info' => 'required|array',
            'contact_info.name' => 'required|string',
            'contact_info.organization' => 'required|string',
            'contact_info.streets' => 'required|array',
            'contact_info.city' => 'required|string',
            'contact_info.province' => 'required|string',
            'contact_info.postal_code' => 'required|string',
            'contact_info.country_code' => 'required|string|size:2',
            'contact_info.voice' => 'required|string',
            'contact_info.email' => 'required|email',
        ]);

        try {
            DB::beginTransaction();

            // Check if the domain belongs to the current user
            if ($domain->owner_id !== Auth::id()) {
                return redirect()->back()
                    ->with('error', 'You do not have permission to update this domain.');
            }

            // Create new contacts
            $contactInfo = $request->contact_info;
            $contacts = [];
            $savedContacts = [];

            foreach (['registrant', 'admin', 'tech'] as $type) {
                $contactId = $type.'-'.Str::random(8);
                $contactData = array_merge($contactInfo, [
                    'id' => $contactId,
                    'fax' => ['number' => '', 'ext' => ''],
                    'disclose' => [],
                ]);

                $result = $this->eppService->createContacts($contactData);
                $response = $this->eppService->getClient()->request($result['frame']);

                if (! $response || ! $response->success()) {
                    throw new Exception("Failed to create {$type} contact");
                }

                $contacts[$type] = [
                    'id' => $contactId,
                    'auth_info' => $result['auth'],
                ];

                // Save contact to database
                $savedContacts[$type] = Contact::create([
                    'contact_id' => $contactId,
                    'name' => $contactInfo['name'],
                    'organization' => $contactInfo['organization'],
                    'street1' => $contactInfo['streets'][0] ?? '',
                    'street2' => $contactInfo['streets'][1] ?? '',
                    'city' => $contactInfo['city'],
                    'province' => $contactInfo['province'],
                    'postal_code' => $contactInfo['postal_code'],
                    'country_code' => $contactInfo['country_code'],
                    'voice' => $contactInfo['voice'],
                    'email' => $contactInfo['email'],
                    'auth_info' => $result['auth'],
                    'disclose' => [],
                    'user_id' => Auth::id(),
                ]);
            }

            // Update domain contacts in EPP
            $frame = $this->eppService->updateDomain(
                $domain->name,
                [$contacts['admin']['id']],
                [$contacts['tech']['id']],
                [], // No host objects to update
                [], // No host attributes to update
                [], // No statuses to update
                []  // No host attributes to remove
            );

            $response = $this->eppService->getClient()->request($frame['frame']);

            if (! $response || ! $response->success()) {
                throw new Exception('Failed to update domain contacts in registry');
            }

            // Update domain contacts in database
            $domain->update([
                'registrant_contact_id' => $savedContacts['registrant']->id,
                'admin_contact_id' => $savedContacts['admin']->id,
                'tech_contact_id' => $savedContacts['tech']->id,
            ]);

            DB::commit();

            return redirect()->route('client.domains', $domain)
                ->with('success', 'Domain contacts updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Domain contact update failed: '.$e->getMessage(), [
                'domain' => $domain->name,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update domain contacts. Please try again or contact support.');
        } finally {
            $this->eppService->disconnect();
        }
    }

    public function editNameservers(Domain $domain)
    {
        $domain=Domain::where(Auth::id(),'owner_id')->firstOrFail();
            return view('client.domains.edit-nameservers',['domain'=>$domain]);
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

            // Update nameservers in EPP
            $frame = $this->eppService->updateDomain(
                $domain->name,
                [], // No admin contacts to update
                [], // No tech contacts to update
                $request->nameservers, // New nameservers as host objects
                [], // No host attributes to update
                [], // No statuses to update
                $domain->nameservers ?? [] // Remove old nameservers
            );

            $response = $this->eppService->getClient()->request($frame['frame']);

            if (! $response || ! $response->success()) {
                throw new Exception('Failed to update domain nameservers in registry');
            }

            // Update nameservers in database
            $domain->update([
                'nameservers' => $request->nameservers,
            ]);

            DB::commit();

            return redirect()->route('client.domains', $domain)
                ->with('success', 'Domain nameservers updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Domain nameserver update failed: '.$e->getMessage(), [
                'domain' => $domain->name,
                'nameservers' => $request->nameservers,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update domain nameservers. Please try again or contact support.');
        } finally {
            $this->eppService->disconnect();
        }
    }
}
