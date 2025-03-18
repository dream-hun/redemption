<?php

namespace App\Livewire;

use App\Models\Domain;
use App\Models\DomainPricing;
use App\Services\Epp\EppService;
use Cknow\Money\Money;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class DomainSearch extends Component
{
    public $domain = '';

    public $extension = '';

    public $results = [];

    public $error = '';

    public $isSearching = false;

    public $cartTotal = 0;

    public $quantity = 1;

    protected EppService $eppService;

    public function boot(EppService $eppService): void
    {
        $this->eppService = $eppService;
    }

    public function mount(): void
    {
        // Set default extension to first TLD
        $firstTld = Cache::remember('active_tld', 3600, function () {
            return DomainPricing::where('status', 'active')->first();
        });

        if ($firstTld) {
            $this->extension = $firstTld->tld;
        }
    }

    public function render(): Factory|Application|View|\Illuminate\View\View
    {
        $tlds = Cache::remember('active_tlds', 3600, function () {
            return DomainPricing::where('status', 'active')->get();
        });

        return view('livewire.domain-search', [
            'tlds' => $tlds,
        ]);
    }

    public function search(): void
    {
        $this->validate([
            'domain' => [
                'required',
                'min:2',
                'regex:/^[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/i',
            ],
            'extension' => 'required',
        ], [
            'domain.regex' => 'Domain name can only contain letters, numbers, and hyphens, and cannot start or end with a hyphen.',
            'extension.required' => 'Please select a domain extension.',
        ]);

        $this->resetExcept('domain', 'extension');
        $this->isSearching = true;

        try {
            // Get cached TLDs
            $tlds = Cache::remember('active_tlds', 3600, function () {
                return DomainPricing::where('status', 'active')->get();
            });

            if ($tlds->isEmpty()) {
                $this->error = 'No TLDs configured in the system.';

                return;
            }

            // Get the selected primary TLD
            $primaryTld = $tlds->where('tld', $this->extension)->first();
            if (! $primaryTld) {
                $this->error = 'Selected extension is not available.';

                return;
            }

            $results = [];
            $cartContent = Cart::getContent();

            // Check primary domain
            $primaryDomainName = strtolower($this->domain.'.'.ltrim($primaryTld->tld, '.'));
            $this->checkAndAddDomain($results, $primaryDomainName, $primaryTld, $cartContent, true);

            // Check all other TLDs
            foreach ($tlds->where('tld', '!=', $primaryTld->tld) as $tld) {
                $domainWithTld = strtolower($this->domain.'.'.ltrim($tld->tld, '.'));
                $this->checkAndAddDomain($results, $domainWithTld, $tld, $cartContent, false);
            }

            $this->results = $results;
            Log::debug('Final search results:', ['count' => count($results)]);

        } catch (Exception $e) {
            Log::error('Domain check error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error = 'An error occurred while searching for domains.';
        } finally {
            $this->isSearching = false;
        }
    }

    /**
     * Check a domain and add it to results if check succeeds
     */
    private function checkAndAddDomain(array &$results, string $domainName, $tld, $cartContent, bool $isPrimary): void
    {
        try {
            $eppResults = $this->eppService->checkDomain([$domainName]);

            if (! empty($eppResults) && isset($eppResults[$domainName])) {
                $result = $eppResults[$domainName];
                $rawPrice = intval($tld->register_price);

                $results[$domainName] = [
                    'available' => $result->available,
                    'reason' => $result->reason,
                    'register_price' => $rawPrice,
                    'transfer_price' => $tld->transfer_price,
                    'renew_price' => $tld->renew_price,
                    'formatted_price' => Money::RWF($rawPrice)->format(),
                    'in_cart' => $cartContent->has($domainName),
                    'is_primary' => $isPrimary,
                ];

                Log::debug('Domain check successful', [
                    'domain' => $domainName,
                    'available' => $result->available,
                    'raw_price' => $rawPrice,
                ]);
            }
        } catch (Exception $e) {
            Log::error('EPP check error for domain:', [
                'domain' => $domainName,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function addToCart($domain, $price): void
    {
        try {

            // Check if domain is already in cart by searching through cart items
            $cartContent = Cart::getContent();
            if ($cartContent->firstWhere('id', $domain)) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Domain is already in your cart.',
                ]);

                return;
            }

            // Add debug logging before cart addition
            \Illuminate\Support\Facades\Log::debug('Adding to cart:', [
                'domain' => $domain,
                'price' => $price,
            ]);

            // Add to cart with proper attributes
            Cart::add([
                'id' => $domain,
                'name' => $domain,
                'price' => $price,
                'quantity' => 1,
                'attributes' => [
                    'domain' => $domain,
                    'user_id' => auth()->id(),
                ],
                'associatedModel' => Domain::class,
            ]);

            // Dispatch event to refresh cart total
            $this->dispatch('refreshCart');

            // Add debug logging after cart addition
            \Illuminate\Support\Facades\Log::debug('Cart after addition:', [
                'total' => Cart::getTotal(),
                'items' => Cart::getContent()->toArray(),
            ]);

            // Update the in_cart status for this domain in results
            if (isset($this->results[$domain])) {
                $this->results[$domain]['in_cart'] = true;
            }

            // Dispatch cart update event
            $this->dispatch('update-cart')->to(CartTotal::class);

        } catch (Exception $e) {
            Log::error('Add to cart error:', [
                'domain' => $domain,
                'price' => $price,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to add domain to cart. Please try again.',
            ]);
        }
    }

    public function removeFromCart($domain): void
    {
        try {
            // Remove from cart
            Cart::remove($domain);

            // Dispatch cart update event
            $this->dispatch('update-cart')->to(CartTotal::class);

            // Update the in_cart status for this domain in results
            if (isset($this->results[$domain])) {
                $this->results[$domain]['in_cart'] = false;
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $domain.' removed from cart.',
            ]);

        } catch (Exception $e) {
            Log::error('Remove from cart error:', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to remove domain from cart. Please try again.',
            ]);
        }
    }
}
