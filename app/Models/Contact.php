<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'fax',
        'fax_extension',
        'email',
        'auth_info',
        'disclose',
        'user_id',
    ];

    protected $casts = [
        'disclose' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
