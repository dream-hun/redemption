<?php

namespace App\Livewire;

use Darryldecode\Cart\Facades\CartFacade as Cart;
use Cknow\Money\Money;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class CartTotal extends Component
{
    protected $listeners = ['refreshCart' => '$refresh'];

    public function getFormattedTotalProperty()
    {
        return Money::RWF(Cart::getTotal())->format();
    }

    public function render(): Factory|Application|View|\Illuminate\View\View
    {
        return view('livewire.cart-total');
    }
}
