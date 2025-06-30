<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DomainCheckRequest;
use App\Http\Requests\Admin\DomainTransferRequest;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Domain;
use App\Models\DomainContact;
use App\Models\Nameserver;
use App\Services\Epp\EppService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class TransferDomainController extends Controller
{
    private EppService $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    public function index(): View
    {
        return view('admin.domains.transfer', [
            'domainCheck' => session('domainCheck', null),
            'authCodeSubmitted' => session('authCodeSubmitted', false),
            'contacts' => Contact::where('user_id', Auth::id())->get(),
            'countries' => Country::all(),
            'cartItems' => Cart::getContent(),
            'total' => Cart::getTotal(),
        ]);
    }

    public function checkDomain(DomainCheckRequest $request): RedirectResponse
    {
        $domainName = $request->domain_name;

        try {
            $existingDomain = Domain::where('name', $domainName)->first();
            // if ($existingDomain) {
            //     return redirect()->route('transfer.index')
            //         ->with('error', 'This domain is already registered with us.')
            //         ->with('domainCheck', [
            //             'status' => 'in_system',
            //             'domain' => $domainName,
            //         ]);
            // }

            $checkResult = $this->eppService->checkDomainForTransfer($domainName);
            if ($checkResult['available']) {
                return redirect()->route('transfer.index')
                    ->with('error', 'This domain is not registered with any registrar.')
                    ->with('domainCheck', [
                        'status' => 'not_registered',
                        'domain' => $domainName,
                    ]);
            }

            return redirect()->route('transfer.index')
                ->with('success', 'Domain is eligible for transfer. Please provide the auth code.')
                ->with('domainCheck', [
                    'status' => 'eligible',
                    'domain' => $domainName,
                ]);
        } catch (Exception $e) {
            Log::error('Domain check failed: '.$e->getMessage(), [
                'domain' => $domainName,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('transfer.index')
                ->with('error', 'Failed to check domain status. '.$e->getMessage())
                ->with('domainCheck', [
                    'status' => 'error',
                    'domain' => $domainName,
                ]);
        }
    }

    public function submitAuthCode(Request $request): RedirectResponse
    {
        $request->validate([
            'domain_name' => ['required', 'string', 'regex:/^[a-zA-Z0-9\-\.]+$/'],
            'auth_info' => ['required', 'string', 'max:255'],
        ]);

        $domainName = $request->domain_name;
        $authInfo = $request->auth_info;

        try {
            $existingDomain = Domain::where('name', $domainName)->first();
            // if ($existingDomain) {
            //     return redirect()->route('transfer.index')
            //         ->with('error', 'This domain is already registered with us.');
            // }

            $checkResult = $this->eppService->checkDomainForTransfer($domainName);
            if ($checkResult['available']) {
                return redirect()->route('transfer.index')
                    ->with('error', 'This domain is not registered with any registrar.');
            }

            return redirect()->route('transfer.index')
                ->with('success', 'Auth code submitted. Please complete the transfer details.')
                ->with('domainCheck', [
                    'status' => 'eligible',
                    'domain' => $domainName,
                    'authInfo' => $authInfo,
                ])
                ->with('authCodeSubmitted', true);
        } catch (Exception $e) {
            Log::error('Auth code submission failed: '.$e->getMessage(), [
                'domain' => $domainName,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('transfer.index')
                ->with('error', 'Failed to process auth code.');
        }
    }

    public function initiateTransfer(DomainTransferRequest $request): RedirectResponse
    {
        $domainName = $request->input('domain_name');
        $authInfo = $request->input('auth_info');
        $period = 1;

        try {
            DB::beginTransaction();

            $existingDomain = Domain::where('name', $domainName)->first();
            // if ($existingDomain) {
            //     throw new Exception('Domain is already registered with us.');
            // }

            $checkResult = $this->eppService->checkDomainForTransfer($domainName);
            if ($checkResult['available']) {
                throw new Exception('Domain is not registered with any registrar.');
            }

            $domain = Domain::create([
                'name' => $domainName,
                'uuid' => Str::uuid(),
                'owner_id' => Auth::id(),
                'registrar' => env('EPP_HOST'),
                'status' => 'pending',
                'registered_at' => now(),
                'expires_at' => now()->addYears($period),
                'registration_period' => $period,
                'auth_code' => $authInfo,
                'whois_privacy' => true,
                'auto_renew' => 0,
                'domain_pricing_id' => 1,
            ]);

            $contacts = [
                'registrant' => $request->input('registrant_contact_id'),
                'admin' => $request->input('admin_contact_id') ?? $request->input('registrant_contact_id'),
                'tech' => $request->input('tech_contact_id') ?? $request->input('registrant_contact_id'),
                'billing' => $request->input('billing_contact_id') ?? $request->input('registrant_contact_id'),
            ];

            foreach ($contacts as $type => $contactId) {
                DomainContact::create([
                    'domain_id' => $domain->id,
                    'contact_id' => $contactId,
                    'type' => $type,
                    'user_id' => Auth::id(),
                ]);
            }

            // Create nameserver records
            $nameservers = $request->input('nameservers', []);
            foreach ($nameservers as $hostname) {

                $dnsProvider = explode('.', $hostname)[1] ?? 'unknown';

                Nameserver::create([
                    'domain_id' => $domain->id,
                    'dns_provider' => $dnsProvider,
                    'hostname' => $hostname,
                    'ipv4_addresses' => null,
                    'ipv6_addresses' => null,
                ]);
            }

            // Send transfer request to registry
            $frame = $this->eppService->transferDomain($domainName, $authInfo, $period.'y');
            $client = $this->eppService->getClient();
            $response = $client->request($frame);

            if (! $response instanceof \AfriCC\EPP\Frame\Response) {
                throw new Exception('Invalid response from registry');
            }
            // dd($response);
            $result = $response->results()[0];
            if ($result->code() < 1000 || $result->code() >= 2000) {
                throw new Exception("Registry error (code: {$result->code()}): {$result->message()}");
            }

            $responseData = $response->data();
            if (! is_array($responseData)) {
                throw new Exception('Unexpected response data format');
            }

            // Update domain status
            $domain->update([
                'status' => 'active',
                'expires_at' => now()->addYears($period),
            ]);
            // Handle cart for payable domains (not ending with .rw)
            $isFreeTransfer = str_ends_with(mb_strtolower($domainName), '.rw');
            if (! $isFreeTransfer) {
                $cartItemId = 'transfer_'.$domainName;
                if (Cart::get($cartItemId)) {
                    return redirect()->route('transfer.index')
                        ->with('warning', 'Domain transfer is already in your cart.');
                }

                $transferPrice = 0.00;
                Cart::add([
                    'id' => $cartItemId,
                    'name' => $domainName,
                    'price' => $transferPrice,
                    'quantity' => 1,
                    'attributes' => [
                        'domain' => $domainName,
                        'type' => 'transfer',
                        'user_id' => Auth::id(),
                        'auth_info' => $authInfo,
                        'registrant_contact_id' => $request->input('registrant_contact_id'),
                        'admin_contact_id' => $request->input('admin_contact_id'),
                        'tech_contact_id' => $request->input('tech_contact_id'),
                        'billing_contact_id' => $request->input('billing_contact_id'),
                        'nameservers' => $nameservers,
                    ],
                ]);

                return redirect()->route('cart.index')
                    ->with('success', 'Domain transfer initiated successfully. Continue on Shopping so u can pay in checkout');

                //  Cart::remove($cartItemId);
            }

            DB::commit();

            return redirect()->route('admin.domains.index')
                ->with('success', 'Domain transfer initiated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Domain transfer failed: '.$e->getMessage(), [
                'domain' => $domainName,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('transfer.index')
                ->with('error', 'Failed to initiate domain transfer: '.$e->getMessage());
        } finally {
            $this->eppService->disconnect();
        }
    }
}
