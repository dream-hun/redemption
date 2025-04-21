<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Nameserver;
use Illuminate\Database\Seeder;

final class NameserverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nameservers = [
            [
                'domain_id' => 1,
                'dns_provider' => null,
                'hostname' => 'ns1.ricta.org.rw',
                'ipv4_addresses' => null,
                'ipv6_addresses' => null,

            ],
            [
                'domain_id' => 1,
                'dns_provider' => null,
                'hostname' => 'ns2.ricta.org.rw',
                'ipv4_addresses' => null,
                'ipv6_addresses' => null,

            ],
            [
                'domain_id' => 1,
                'dns_provider' => null,
                'hostname' => 'ns1.dns-parking.com',
                'ipv4_addresses' => null,
                'ipv6_addresses' => null,

            ],
            [
                'domain_id' => 1,
                'dns_provider' => null,
                'hostname' => 'ns2.dns-parking.com',
                'ipv4_addresses' => null,
                'ipv6_addresses' => null,

            ],
            [
                'domain_id' => 1,
                'dns_provider' => null,
                'hostname' => 'ns5.dns-parking.com',
                'ipv4_addresses' => null,
                'ipv6_addresses' => null,

            ],

        ];

        Nameserver::insert($nameservers);
    }
}
