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
        if (Auth::check()) {
            // For authenticated users, show their cart items
            $this->items = Cart::where('user_id', Auth::id())->get();
        } else {
            // For guests, show items matching their current session
            $this->items = Cart::where('session_id', session()->getId())
                              ->whereNull('user_id')
                              ->get();
        }
        
        $this->calculateTotals();
    }

    private function calculateTotals()
    {
        if ($this->items->isNotEmpty()) {
            $subtotal = $this->items->sum(function($item) {
                return $item->price * $item->period;
            });
            
            $this->subtotalAmount = $subtotal;
            $this->taxAmount = $subtotal * 0.18; // 18% VAT
            $this->totalAmount = $subtotal + $this->taxAmount;
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
        $item = Cart::where('uuid', $uuid)
            ->when(Auth::check(), function($query) {
                return $query->where('user_id', Auth::id());
            }, function($query) {
                return $query->where('session_id', session()->getId())
                           ->whereNull('user_id');
            })
            ->first();
        
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
        $item = Cart::where('uuid', $uuid)
            ->when(Auth::check(), function($query) {
                return $query->where('user_id', Auth::id());
            }, function($query) {
                return $query->where('session_id', session()->getId())
                           ->whereNull('user_id');
            })
            ->first();
        
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
