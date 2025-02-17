<?php

namespace App\Models;

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
    // Mutator for created_at
    protected function createdAt(): Attribute
    {
        return Attribute::get(fn ($value) => $value ? $value->format('Y-m-d') : null);
    }

    // Mutator for expiry_date
    protected function expiryDate(): Attribute
    {
        return Attribute::get(fn ($value) => $value ? $value->format('Y-m-d') : null);
    }
    protected function registeredAt(): Attribute
    {
        return Attribute::get(fn ($value) => $value ? $value->format('Y-m-d') : null);
    }
}
