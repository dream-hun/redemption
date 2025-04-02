<?php

namespace App\Http\Controllers\Admin;

use AfriCC\EPP\Frame\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DomainRenewalRequest;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Domain;
use App\Models\DomainContact;
use App\Services\Epp\EppService;
use Carbon\Carbon;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use DateTime;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class RenewDomainController extends Controller
{
    protected EppService $eppService;

    public function __construct(EppService $eppService)
    {
        $this->eppService = $eppService;
    }

    public function index(string $uuid): View|RedirectResponse
    {
        $domain = Domain::where('uuid', $uuid)->firstOrFail();

        // Check if user owns the domain
        if ($domain->owner_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You do not have permission to renew this domain.');
        }

        $cartItems = Cart::getContent();
        $total = Cart::getTotal();

        // Get all contacts for the current user with essential fields
        $contacts = Contact::where('user_id', Auth::id())
            ->select('id', 'contact_id', 'name', 'organization', 'email', 'voice')
            ->orderBy('created_at', 'desc')
            ->get();

        $countries = Country::all();

        // Try to get domain information from EPP
        try {
            $eppInfo = $this->eppService->getDomainInfo($domain->name);
            
            // Log the raw response for debugging
            Log::debug('Registry response for domain info', [
                'domain' => $domain->name,
                'response' => $eppInfo
            ]);
            
            if ($eppInfo && isset($eppInfo['infData'])) {
                $eppInfo = $eppInfo['infData'];
                
                // Update domain with registry data if available
                if (isset($eppInfo['exDate'])) {
                    $expiryDate = Carbon::parse($eppInfo['exDate']);
                    $domain->update(['expires_at' => $expiryDate]);
                    $domain->refresh();
                    
                    Log::info('Updated domain with registry data', [
                        'domain' => $domain->name,
                        'registry_expiry' => $eppInfo['exDate'],
                        'db_expiry' => $domain->expires_at
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::warning('EPP service warning - continuing with renewal process', [
                'domain' => $domain->name,
                'error' => $e->getMessage()
            ]);
            $eppInfo = [];
        }

        return view('admin.domains.renewal', [
            'domain' => $domain,
            'cartItems' => $cartItems,
            'total' => $total,
            'contacts' => $contacts,
            'countries' => $countries,
            'eppInfo' => $eppInfo ?? [],
        ]);
    }

    /**
     * Add a domain to the cart for renewal
     *
     * @param string $uuid
     * @return RedirectResponse
     */
    public function addToCart(string $uuid): RedirectResponse
    {
        $domain = Domain::where('uuid', $uuid)->firstOrFail();

        // Check if user owns the domain
        if ($domain->owner_id !== auth()->id()) {
            return redirect()->route('admin.domains.index')
                ->with('error', 'You are not authorized to renew this domain.');
        }

        try {
            // Check if domain is already in cart
            $cartContent = Cart::getContent();
            $cartItemId = 'renew_'.$domain->name;

            if ($cartContent->has($cartItemId)) {
                return redirect()->route('admin.domains.renewal.index', ['uuid' => $domain->uuid])
                ->with('warning', 'Domain renewal is already in your cart.');
            }

            // Get renewal price from domain pricing
            $renewPrice = $domain->domainPricing->renew_price;

            if (! $renewPrice) {
                return redirect()->route('admin.domains.index')
                    ->with('error', 'Renewal price not found for this domain.');
            }

            // Add to cart with proper attributes
            Cart::add([
                'id' => $cartItemId,
                'name' => $domain->name,
                'price' => $renewPrice,
                'quantity' => 1,
                'attributes' => [
                    'domain' => $domain->name,
                    'type' => 'renewal',
                    'user_id' => auth()->id(),
                    'domain_id' => $domain->id,
                ],
                'associatedModel' => Domain::class,
            ]);

            Log::debug('Domain renewal added to cart:', [
                'domain' => $domain->name,
                'price' => $renewPrice,
                'cart_id' => $cartItemId,
            ]);

            return redirect()->route('admin.domains.renewal.index', ['uuid' => $domain->uuid])
                ->with('success', 'Domain renewal added to cart successfully.');

        } catch (Exception $e) {
            Log::error('Failed to add domain renewal to cart:', [
                'domain' => $domain->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.domains.index')
                ->with('error', 'Failed to add domain renewal to cart. '.$e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function renew(DomainRenewalRequest $request, string $uuid): RedirectResponse
    {
        $domain = Domain::where('uuid', $uuid)->firstOrFail();

        if ($domain->owner_id !== auth()->user()->id) {
            return redirect()->route('admin.domains.index')->with('error', 'You are not allowed to renew domains.');
        }

        try {
            DB::beginTransaction();
            try {
                // Check if domain exists in cart
                $cartItemId = 'renew_'.$domain->name;
                if (!Cart::get($cartItemId)) {
                    throw new Exception('Domain renewal not found in cart. Please add it to cart first.');
                }

                /**
                 * Get current domain information from registry
                 * This is critical for renewal as we need the exact expiry date
                 */
                try {
                    // Get domain info from registry
                    $domainInfo = $this->eppService->getDomainInfo($domain->name);
                    
                    // Log the raw response for debugging
                    Log::debug('Registry response for renewal', [
                        'domain' => $domain->name,
                        'response' => $domainInfo
                    ]);
                    
                    if (!$domainInfo || !isset($domainInfo['infData']['exDate'])) {
                        throw new Exception('Could not retrieve domain expiry date from registry');
                    }
                    
                    // Get the exact expiry date from registry - DO NOT MODIFY THIS STRING
                    $rawExpiryDate = $domainInfo['infData']['exDate'];
                    
                    // Parse for our database and display
                    $registryExpiryDate = Carbon::parse($rawExpiryDate);
                    
                    // Update domain with registry data
                    $domain->update([
                        'expires_at' => $registryExpiryDate
                    ]);
                    
                    // Reload domain
                    $domain->refresh();
                    
                    // Log the domain info
                    Log::info('Retrieved domain info from registry', [
                        'domain' => $domain->name,
                        'registry_expiry_raw' => $rawExpiryDate,
                        'registry_expiry_parsed' => $registryExpiryDate->format('Y-m-d'),
                        'db_expiry' => $domain->expires_at->format('Y-m-d')
                    ]);
                } catch (Exception $e) {
                    Log::error('Failed to get domain info from registry', [
                        'domain' => $domain->name,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);

                    throw new Exception('Registry error: ' . $e->getMessage());
                }

                // Create EPP frame for domain renewal using the EXACT registry expiry date
                // Convert period to integer to avoid Carbon::rawAddUnit error
                $periodYears = (int) $request->period;
                $frame = $this->eppService->renewDomain(
                    $domain->name, 
                    $rawExpiryDate, 
                    $periodYears . 'y'
                );
                
                // Log the renewal request
                Log::info('Sending domain renewal request', [
                    'domain' => $domain->name,
                    'period' => $periodYears . 'y',
                    'registry_expiry' => $rawExpiryDate
                ]);
                
                // Send the request to registry
                $client = $this->eppService->getClient();
                $response = $client->request($frame);
                
                if (!($response instanceof Response)) {
                    throw new Exception('Invalid response received from registry');
                }
                
                // Get the result details
                $result = $response->results()[0];
                if (!$result) {
                    throw new Exception('No result in registry response');
                }
                
                // Log the response
                Log::debug('Registry response received', [
                    'domain' => $domain->name,
                    'code' => $result->code(),
                    'message' => $result->message(),
                    'data' => $response->data()
                ]);
                
                // Check if the response indicates success (1000-series codes are success)
                if ($result->code() < 1000 || $result->code() >= 2000) {
                    throw new Exception("Registry error (code: {$result->code()}): {$result->message()}");
                }
                
                // Calculate new expiry date
                $newExpiryDate = $registryExpiryDate->copy()->addYears($periodYears);
                
                // Log successful renewal
                Log::info('Domain renewal successful', [
                    'domain' => $domain->name,
                    'old_expiry' => $registryExpiryDate->format('Y-m-d'),
                    'new_expiry' => $newExpiryDate->format('Y-m-d')
                ]);

                // Update domain expiry in our database
                $domain->update([
                    'expires_at' => $newExpiryDate,
                    'last_renewal_at' => now()
                ]);

                // Remove domain from cart
                Cart::remove($cartItemId);

                DB::commit();

                return redirect()->route('admin.domains.index')
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
                'user_id' => auth()->user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to renew domain. Please try again or contact support. Error: '.$e->getMessage());
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Domain renewal failed with throwable: '.$e->getMessage(), [
                'domain' => $domain->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new Exception('Domain renewal failed: '.$e->getMessage());
        } finally {
            $this->eppService->disconnect();
        }
    }
}
