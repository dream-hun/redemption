<?php

declare(strict_types=1);

namespace App\Models;

use Cknow\Money\Money;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

final class DomainPricing extends Model
{
    public const STATUS_SELECT = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

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

    public function formatedRegisterPrice(): Money
    {
        return Money::RWF($this->register_price);
    }

    public function formatedTransferPrice(): Money
    {
        return Money::RWF($this->transfer_price);
    }

    public function formatedRenewPrice(): Money
    {
        return Money::RWF($this->renew_price);
    }

    public function formatedRedemptionPrice(): Money
    {
        return Money::RWF($this->redemption_price);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
