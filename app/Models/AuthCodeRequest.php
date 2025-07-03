<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class AuthCodeRequest extends Model
{
    protected $fillable = [
        'domain_id',
        'user_id',
        'auth_code',
        'recipient_email',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
