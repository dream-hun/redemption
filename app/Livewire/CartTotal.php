<?php

declare(strict_types=1);

namespace App\Livewire;

use Cknow\Money\Money;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

final class CartTotal extends Component
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
