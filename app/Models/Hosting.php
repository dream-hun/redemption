<?php

namespace App\Models;

use App\Enums\HostingStatus;
use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hosting extends Model
{
    protected $fillable = [
        'name',
        'slug', 'icon', 'status', 'price', 'period',

    ];

    protected $casts = [
        'status' => HostingStatus::class,
        'period' => 'integer',
        'price' => 'integer',
    ];

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'host_features')->withPivot('quantity','status');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function formattedPricing(): Money
    {
        return Money::RWF($this->price);
    }

}
