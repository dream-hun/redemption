<?php

namespace App\Livewire;

use Cknow\Money\Money;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class CartComponent extends Component
{
    public $items = [];

    public $subtotalAmount = 0;

    public $taxAmount = 0;

    public $totalAmount = 0;

    public function mount(): void
    {
        $this->refreshCart();
    }

    #[On('update-cart')]
    public function refreshCart(): void
    {
        try {
            $cartContent = Cart::getContent();
            $this->items = $cartContent;
            $this->calculateTotals();
        } catch (\Exception $e) {
            \Log::error('Error refreshing cart: '.$e->getMessage());
            $this->items = collect();
            $this->calculateTotals();
        }
    }

    private function calculateTotals(): void
    {
        try {
            if ($this->items->isNotEmpty()) {
                $subtotal = $this->items->sum(function ($item) {
                    return floatval($item->price) * $item->quantity;
                });

                $this->subtotalAmount = $subtotal;
                $this->taxAmount = $subtotal * 0.18; // 18% VAT
                $this->totalAmount = $subtotal + $this->taxAmount;
            } else {
                $this->subtotalAmount = 0;
                $this->taxAmount = 0;
                $this->totalAmount = 0;
            }
        } catch (\Exception $e) {
            \Log::error('Error calculating totals: '.$e->getMessage());
            $this->subtotalAmount = 0;
            $this->taxAmount = 0;
            $this->totalAmount = 0;
        }
    }

    public function getFormattedSubtotalProperty(): string
    {
        return Money::RWF($this->subtotalAmount)->format();
    }

    public function getFormattedTaxProperty(): string
    {
        return Money::RWF($this->taxAmount)->format();
    }

    public function getFormattedTotalProperty(): string
    {
        return Money::RWF($this->totalAmount)->format();
    }

    public function updateQuantity($id, $quantity): void
    {
        try {
            if ($quantity > 0) {
                Cart::update($id, [
                    'quantity' => [
                        'relative' => false,
                        'value' => $quantity,
                    ],
                ]);

                $this->refreshCart();
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Quantity updated successfully',
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error updating quantity: '.$e->getMessage());
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
            $this->refreshCart();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Item removed from cart successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error removing item: '.$e->getMessage());
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
