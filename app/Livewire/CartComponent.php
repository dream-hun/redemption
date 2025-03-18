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
        $this->items = Cart::getContent();
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
                Cart::update($id, [
                    'quantity' => [
                        'relative' => false,
                        'value' => (int) $quantity,
                    ],
                ]);

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
