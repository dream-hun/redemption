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
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

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
            if (Auth::check()) {
                $model->user_id = Auth::id();
            }
        });
    }

    public function formatedPrice(): Money
    {
        return Money::RWF($this->price);
    }

    protected $casts = [
        'session_id' => 'string',
    ];
}
