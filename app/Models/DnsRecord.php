<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class DnsRecord extends Model
{
    protected $fillable = [
        'domain_id',
        'type',      // A, AAAA, MX, CNAME, TXT, etc.
        'name',      // Hostname/subdomain
        'value',     // IP address, hostname, or text content
        'priority',  // Mainly used for MX records
        'ttl',        // Time To Live in seconds
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }
}
