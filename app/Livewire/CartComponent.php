<?php

namespace App\Livewire;

use Darryldecode\Cart\Facades\CartFacade as Cart;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Cknow\Money\Money;

class CartComponent extends Component
{
    public $items;
    public $subtotalAmount = 0;
    public $totalAmount = 0;

    public function mount(): void
    {
        $this->updateCartTotal();
    }

    #[On('update-cart')]
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
            if ($quantity > 0) {
                Cart::update($id, [
                    'quantity' => [
                        'relative' => false,
                        'value' => $quantity,
                    ],
                ]);

                // Update local cart data
                $this->updateCartTotal();

                // Dispatch cart update event to CartTotal component
                $this->dispatch('update-cart')->to(CartTotal::class);


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

            // Update local cart data
            $this->updateCartTotal();

            // Dispatch cart update event to CartTotal component
            $this->dispatch('update-cart')->to(CartTotal::class);

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
