<?php

namespace Database\Seeders;

use App\Models\Domain;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DomainSeeder extends Seeder
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
                'registrar' => 'BLUHUB',
                'registrant_contact_id' => 2,
                'admin_contact_id' => 1,
                'tech_contact_id' => 3,
                'billing_contact_id' => 4,
                'status' => 'active',
                'registered_at' => '2025-02-28 04:32:28',
                'expires_at' => '2026-02-28 04:32:28',
                'auto_renew' => true,
                'dns_provider' => null,
                'nameservers' => json_encode(
                    [
                        'ns1.dns-parking.com', 'ns2.dns-parking.com', 'ns1.dns-parking.com',
                        'ns2.dns-parking.com', 'dns1.namecheaphosting.com', 'dns2.namecheaphosting.com',
                        'ns3.dns-parking.com', 'ns4.dns-parking.com',
                    ]),
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
