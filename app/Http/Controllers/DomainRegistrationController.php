<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Domain;
use App\Services\Epp\EppService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Country;
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
                return redirect()->route('cart')->with('error', 'Your cart is empty.');
            }

            // Create contact IDs for registrant, admin, and tech
            $contactIds = [
                'registrant' => 'REG-' . Str::random(8),
                'admin' => 'ADM-' . Str::random(8),
                'tech' => 'TECH-' . Str::random(8)
            ];

            // Create contacts with EPP
            $contactInfo = $request->contact_info;
            $contacts = [];

            foreach ($contactIds as $type => $id) {
                $contactData = array_merge($contactInfo, [
                    'id' => $id,
                    'fax' => ['number' => '', 'ext' => ''],
                    'disclose' => []
                ]);

                $result = $this->eppService->createContacts($contactData);
                $response = $this->eppService->getClient()->request($result['frame']);

                if (!$response || !$response->success()) {
                    throw new Exception("Failed to create {$type} contact");
                }

                $contacts[$type] = [
                    'id' => $id,
                    'auth_info' => $result['auth']
                ];
            }

            // Register each domain
            $registeredDomains = [];
            foreach ($cartItems as $item) {
                // Create domain with EPP
                $frame = $this->eppService->createDomain(
                    $item->domain,
                    (string) $item->period . 'y',
                    [], // hostAttrs - empty for now, can be added later
                    $contacts['registrant']['id'],
                    $contacts['admin']['id'],
                    $contacts['tech']['id']
                );

                $response = $this->eppService->getClient()->request($frame);

                if (!$response || !$response->success()) {
                    throw new Exception("Failed to register domain: {$item->domain}");
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
                    'nameservers' => config('app.default_nameservers', []),
                    'whois_privacy' => true
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
            Log::error('Domain registration failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'cart_items' => $cartItems ?? [],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to register domains. Please try again or contact support.');
        } finally {
            $this->eppService->disconnect();
        }
    }
}
