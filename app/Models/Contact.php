<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\ContactScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[ScopedBy([ContactScope::class])]
final class Contact extends Model
{
    protected $fillable = [
        'contact_id',
        'name',
        'organization',
        'street1',
        'street2',
        'city',
        'province',
        'postal_code',
        'country_code',
        'voice',
        'fax_number',
        'fax_ext',
        'email',
        'auth_info',
        'epp_status',
        'user_id',
    ];

    protected $casts = [
        'disclose' => 'array',
    ];

    public static function generateContactIds(): string
    {
        // Format: RW-XXXX-YYYY where X is a random letter, and Y is a random number
        $letters = mb_strtoupper(Str::random(4));
        $numbers = mb_str_pad(rand(1, 9999).'', 4, '0', STR_PAD_LEFT);

        return "RW-$letters-$numbers";
    }

    public static function boot(): void
    {
        parent::boot();
        self::creating(function ($model): void {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function domainContacts(): HasMany
    {
        return $this->hasMany(DomainContact::class, 'contact_id');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
