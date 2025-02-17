<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Domain extends Model
{
    protected $fillable = [
        'name',
        'auth_code',
        'registrar',
        'status',
        'registered_at',
        'expires_at',
        'auto_renew',
        'dns_provider',
        'nameservers',
        'owner_id',
        'ssl_status',
        'ssl_expires_at',
        'whois_privacy',
        'registration_period',
        'last_renewal_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'expires_at' => 'datetime',
        'ssl_expires_at' => 'datetime',
        'last_renewal_at' => 'datetime',
        'auto_renew' => 'boolean',
        'whois_privacy' => 'boolean',
        'nameservers' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function isExpiring($days = 30)
    {
        return $this->expires_at->diffInDays(now()) <= $days;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getDaysUntilExpiration()
    {
        return now()->diffInDays($this->expires_at);
    }

    protected function registeredAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->format('M d, Y')
        );
    }

    protected function expiresAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->format('M d, Y')
        );
    }

    protected function sslExpiresAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value)->format('M d, Y') : null
        );
    }

    protected function lastRenewalAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value)->format('M d, Y') : null
        );
    }
}
