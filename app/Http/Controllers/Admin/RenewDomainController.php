<?php

namespace App\Http\Controllers\Admin;

use AfriCC\EPP\Frame\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DomainRenewalRequest;
use App\Models\Domain;
use App\Services\Epp\EppService;
use Carbon\Carbon;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Exception;
use Illuminate\Http\RedirectResponse;
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

    /**
     * Add a domain to the cart for renewal
     *
     * @param  Domain  $domain  The domain to be renewed
     * @return RedirectResponse
     */
    public function addToCart(Domain $domain)
    {
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
                return redirect()->route('contacts.create', ['domain' => $domain])
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
                'name' => 'Renewal: '.$domain->name,
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

            return redirect()->route('contacts.create', ['domain' => $domain->name])
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
    public function renew(DomainRenewalRequest $request, Domain $domain)
    {
        if ($domain->owner_id !== auth()->user()->id) {
            return redirect()->route('admin.domains.index')->with('error', 'You are not allowed to renew domains.');
        }

        try {
            DB::beginTransaction();
            try {
                /**
                 * Get current domain information in the registry
                 */
                $domainInformation = $this->eppService->getDomainInfo($domain);
                // Parse the registry expiry date
                $registryExpiryDate = Carbon::parse($domainInformation['infData']['exDate']);

                Log::info('Retrieved domain info from registry',
                    ['domain' => $domain->name, 'registry_expiry' => $registryExpiryDate->format('Y-m-d'), 'local_expiry' => $domain->expires_at]
                );

                // Create EPP frame for domain renewal using registry expiry date
                $frame = $this->eppService->renewDomain($domain->name, $registryExpiryDate, $request->period.'y');

                // Log the request before sending
                Log::info('Sending domain renewal request', ['domain' => $domain->name, 'period' => $request->period.'y', 'registry_expiry' => $registryExpiryDate->format('Y-m-d'),
                ]);

                $client = $this->eppService->getClient();
                $response = $client->request($frame);
                Log::info($response);

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
                $domain->update(['expires_at' => $newExpiryDate, 'last_renewal_at' => now()]);

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
                'user_id' => auth()->user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to renew domain. Please try again or contact support. Error: '.$e->getMessage());
        } catch (Throwable $e) {
            throw new Exception('Domain renewal failed: '.$e->getMessage());
        } finally {
            $this->eppService->disconnect();
        }
    }
}
