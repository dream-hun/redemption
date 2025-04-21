<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Domain;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domains = [
            ['id' => 1,
                'uuid' => Str::uuid(),
                'name' => 'blutest.co.rw',
                'auth_code' => 'AL4HTCJI8x5NU0MA',
                'registrar' => 'RICTA',
                'status' => 'active',
                'registered_at' => '2025-02-28 04:32:28',
                'expires_at' => '2026-02-28 04:32:28',
                'auto_renew' => true,
                'owner_id' => 1,
                'domain_pricing_id' => 2,
                'ssl_status' => null,
                'ssl_expires_at' => null,
                'whois_privacy' => false,
                'registration_period' => 1,
                'last_renewal_at' => '2025-02-28 04:32:28',
                'created_at' => '2025-02-28 04:32:28',
                'updated_at' => '2025-03-06 21:01:14',
            ],

        ];
        Domain::insert($domains);
    }
}
