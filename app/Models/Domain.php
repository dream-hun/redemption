<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

final class Domain extends Model
{
    public const STATUS_SELECT = [
        'active' => 'Active',
        'pending' => 'Pending',
        'expired' => 'Expired',
        'suspended' => 'Suspended',
    ];

    protected $fillable = [
        'uuid',
        'name',
        'auth_code',
        'registrar',
        'status',
        'domain_pricing_id',
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
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function domainPricing(): BelongsTo
    {
        return $this->belongsTo(DomainPricing::class);
    }

    public function nameservers(): HasMany
    {
        return $this->hasMany(Nameserver::class, 'domain_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(DomainContact::class, 'domain_id');
    }

    public function isExpiring($days = 30): bool
    {
        return $this->expires_at->diffInDays(now()) <= $days;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getDaysUntilExpiration(): float
    {
        return now()->diffInDays($this->expires_at);
    }

    public function registeredAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value): string => Carbon::parse($value)->format('M d, Y')
        );
    }

    public function expiresAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value): string => Carbon::parse($value)->format('M d, Y')
        );
    }

    public function sslExpiresAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value): ?string => $value ? Carbon::parse($value)->format('M d, Y') : null
        );
    }

    public function lastRenewalAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value): ?string => $value ? Carbon::parse($value)->format('M d, Y') : null
        );
    }

    public function authCodeRequests()
    {
        return $this->hasMany(AuthCodeRequest::class);
    }

    public function generateAuthCode(): string
    {
        if ($this->auth_code) {
            return $this->auth_code;
        }
        $authCode = Str::random(16);
        $this->update(['auth_code' => $authCode]);

        return $authCode;
    }

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function ($model): void {
            // Generate UUID if not provided
            if (! $model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });

    }
}
