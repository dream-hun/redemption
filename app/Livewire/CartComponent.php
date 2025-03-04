<?php

namespace App\Livewire;

use App\Models\Cart;
use Cknow\Money\Money;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class CartComponent extends Component
{
    public $items = [];
    public $subtotalAmount = 0;
    public $taxAmount = 0;
    public $totalAmount = 0;

    public function mount()
    {
        $this->refreshCart();
    }

    public function refreshCart()
    {
        $this->items = Cart::where(function($query) {
            $query->where('user_id', Auth::id())
                  ->orWhere('session_id', session()->getId());
        })->get();
        
        $this->calculateTotals();
    }

    private function calculateTotals()
    {
        if ($this->items->isNotEmpty()) {
            $firstItem = $this->items->first();
            $this->subtotalAmount = $firstItem->subTotal()->getAmount();
            $this->taxAmount = $firstItem->getTax()->getAmount();
            $this->totalAmount = $firstItem->getTotal()->getAmount();
        } else {
            $this->subtotalAmount = 0;
            $this->taxAmount = 0;
            $this->totalAmount = 0;
        }
    }

    public function getFormattedSubtotalProperty()
    {
        return Money::RWF($this->subtotalAmount)->format();
    }

    public function getFormattedTaxProperty()
    {
        return Money::RWF($this->taxAmount)->format();
    }

    public function getFormattedTotalProperty()
    {
        return Money::RWF($this->totalAmount)->format();
    }

    public function updatePeriod($uuid, $period)
    {
        $item = Cart::where('uuid', $uuid)->first();
        
        if ($item) {
            $item->update([
                'period' => $period,
            ]);

            $this->refreshCart();
            session()->flash('message', 'Period updated successfully');
        }
    }

    public function removeItem($uuid)
    {
        $item = Cart::where('uuid', $uuid)->first();
        
        if ($item) {
            $item->delete();
            $this->refreshCart();
            session()->flash('message', 'Item removed from cart');
        }
    }

    public function render()
    {
        return view('livewire.cart-component');
    }
}
