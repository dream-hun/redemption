<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Contact extends Model
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class, 'registrant_contact_id')
            ->orWhere('admin_contact_id', $this->id)
            ->orWhere('tech_contact_id', $this->id)
            ->orWhere('billing_contact_id');
    }

    public static function generateContactIds(): string
    {
        // Format: RW-XXXX-YYYY where X is random letter and Y is random number
        $letters = strtoupper(Str::random(4));
        $numbers = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return "RW-{$letters}-{$numbers}";
    }


    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
