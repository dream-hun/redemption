<?php

namespace App\Models;

use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'domain',
        'host_id',
        'price',
        'period',
        'subtotal',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
            $model->session_id = session()->getId();
            $model->period = $model->period ?? 1; // Set default period if not provided
            if (Auth::check()) {
                $model->user_id = Auth::id();
            }
        });
    }

    public function formatedPrice(): Money
    {
        return Money::RWF($this->price * $this->period);
    }

    public function getBasePrice(): Money
    {
        return Money::RWF($this->price);
    }

    public function subTotal(): Money
    {
        $subtotal = Cart::Where('user_id', Auth::id())->get()->sum(function ($item) {
            return $item->price * $item->period;
        });

        return Money::RWF($subtotal);
    }

    public function getTax(): Money
    {
        $subtotal = $this->subTotal()->getAmount();
        $tax = $subtotal * 18 / 100;

        return Money::RWF($tax);
    }

    public function getTotal(): Money
    {
        $subtotal = $this->subTotal()->getAmount();
        $tax = $subtotal * 18 / 100;
        $total = $subtotal + $tax;

        return Money::RWF($total);
    }

    protected $casts = [
        'session_id' => 'string',
        'price' => 'integer',
        'period' => 'integer',
    ];
}
