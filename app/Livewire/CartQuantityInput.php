<?php

namespace App\Livewire;

use App\Models\Domain;
use Cknow\Money\Money;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class CartQuantityInput extends Component
{
    protected $listeners = ['refreshCart' => '$refresh'];
    public $periode = 1;
    public $quantityPrevious = 0;
    public $total = 0;
    public $cartitem = [];

    public $domainId = '';
    public function mount($domain_id)
    {
        $this->domainId = $domain_id;
        $this->cartitem = Cart::get($domain_id);
        $this->quantityPrevious = $this->cartitem['quantity'];
        $this->periode = $this->quantityPrevious;
        $this->total =  Cart::getTotal();
    }
    public function getFormattedTotalProperty()
    {
        return Money::RWF(Cart::getTotal())->format();
    }

    public function render(): Factory|Application|View|\Illuminate\View\View
    {
        return view('livewire.cart-quantity-input');
    }
    public function handleCartPeriodCount()
    {
        Cart::remove($this->domainId);
        Cart::add([
            'id' => $this->domainId,
            'name' => $this->cartitem['name'],
            'price' =>  $this->cartitem['price'],
            'quantity' => $this->periode,
            'attributes' => [
                'domain' => $this->cartitem['name'],
                'type' => 'renewal',
                'user_id' => auth()->id(),
                'domain_id' => $this->domainId,
            ],
            'associatedModel' => Domain::class,
        ]);

        $this->total =  Cart::getTotal();
    }
}
