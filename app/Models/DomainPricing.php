<?php

namespace App\Models;

use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Model;

class DomainPricing extends Model
{
    protected $casts = [
        'register_price' => 'integer',
        'transfer_price' => 'integer',
        'renew_price' => 'integer',
        'grace' => 'integer',
        'redemption_price' => 'integer',
    ];

    protected $fillable = [
        'tld',
        'register_price',
        'transfer_price',
        'renew_price',
        'grace',
        'redemption_price',
        'status',
    ];

    public function formatedRegisterPrice(): string
    {
        return Money::RWF($this->register_price);
    }

    public function formatedTransferPrice(): string
    {
        return Money::RWF($this->transfer_price);
    }

    public function formatedRenewPrice(): string
    {
        return Money::RWF($this->renew_price);
    }

    public function formatedRedemptionPrice(): string
    {
        return Money::RWF($this->redemption_price);
    }
}
