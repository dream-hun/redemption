<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Nameserver extends Model
{
    protected $fillable = [
        'hostname',
        'ipv4_addresses',
        'ipv6_addresses',
        'domain_id',
    ];

    protected $casts = [
        'ipv4_addresses' => 'array',
        'ipv6_addresses' => 'array',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
