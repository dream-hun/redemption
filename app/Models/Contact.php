<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'contact_id',
        'contact_type',
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
            ->orWhere('tech_contact_id', $this->id);
    }
}
