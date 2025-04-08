<?php

namespace App\Livewire;

use Cknow\Money\Money;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class CartComponent extends Component
{
    public $items;

    public $subtotalAmount = 0;

    public $totalAmount = 0;

    protected $listeners = ['refreshCart' => '$refresh'];

    public function mount(): void
    {
        $this->updateCartTotal();
    }

    public function updateCartTotal(): void
    {
        // Get cart content and maintain original order
        $cartContent = Cart::getContent();

        // Sort by creation timestamp to maintain consistent order
        // This ensures items stay in the same order regardless of updates
        $this->items = $cartContent->sortBy(function ($item) {
            return $item->attributes->get('added_at', 0);
        });

        $this->subtotalAmount = Cart::getSubTotal();
        $this->totalAmount = Cart::getTotal();
    }

    public function getFormattedSubtotalProperty(): string
    {
        return Money::RWF($this->subtotalAmount)->format();
    }

    public function getFormattedTotalProperty(): string
    {
        return Money::RWF($this->totalAmount)->format();
    }

    public function updateQuantity($id, $quantity): void
    {
        try {
            if ($quantity > 0 && $quantity <= 10) {
                // Get current item to preserve its attributes
                $currentItem = Cart::get($id);

                // Update quantity while preserving attributes
                Cart::update($id, [
                    'quantity' => [
                        'relative' => false,
                        'value' => (int) $quantity,
                    ],
                ]);

                // Make sure we preserve the original added_at timestamp
                // This ensures the item maintains its position in the list
                if (! $currentItem->attributes->has('added_at')) {
                    Cart::update($id, [
                        'attributes' => [
                            'added_at' => now()->timestamp,
                        ],
                    ]);
                }

                $this->updateCartTotal();
                $this->dispatch('refreshCart');

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Quantity updated successfully',
                ]);
            }
        } catch (Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update quantity',
            ]);
        }
    }

    public function removeItem($id): void
    {
        try {
            Cart::remove($id);
            $this->updateCartTotal();
            $this->dispatch('refreshCart');

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Item removed from cart successfully',
            ]);
        } catch (Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to remove item from cart',
            ]);
        }
    }

    public function render(): Factory|Application|View|\Illuminate\View\View
    {
        return view('livewire.cart-component');
    }
}
