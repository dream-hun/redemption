<?php

namespace App\Livewire;

use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;

class CartTotal extends Component
{
    public $total = 0;

    public function mount(): void
    {
        $this->updateCartTotal();
    }

    #[On('update-cart')]
    public function updateCartTotal(): void
    {

        $this->total = Cart::getTotal();
    }

    public function render(): Factory|Application|View|\Illuminate\View\View
    {
        return view('livewire.cart-total');
    }
}
