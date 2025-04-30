<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferInvitation extends Model
{
    protected $fillable = [
        'domain_id', 'sender_id', 'recipient_email', 'auth_code',
        'token', 'expires_at', 'accepted_at', 'accepted_by_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by_id');
    }
}