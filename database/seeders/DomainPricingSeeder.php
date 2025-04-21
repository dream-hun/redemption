<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DomainPricing;
use Illuminate\Database\Seeder;

final class DomainPricingSeeder extends Seeder
{
    public function run(): void
    {
        $tlds = [
            [
                'tld' => '.rw',
                'register_price' => 16000,
                'renew_price' => 17000,
                'transfer_price' => 0,
                'grace' => 30,
                'redemption_price' => 4000,
                'status' => 'active',
            ],
            [
                'tld' => '.co.rw',
                'register_price' => 12000,
                'renew_price' => 13000,
                'transfer_price' => 0,
                'grace' => 30,
                'redemption_price' => 4000,
                'status' => 'active',
            ],
            [
                'tld' => '.org.rw',
                'register_price' => 12000,
                'renew_price' => 1300,
                'transfer_price' => 0,
                'grace' => 30,
                'redemption_price' => 4000,
                'status' => 'active',
            ],
            [
                'tld' => '.ac.rw',
                'register_price' => 13000,
                'renew_price' => 15000,
                'transfer_price' => 0,
                'grace' => 30,
                'redemption_price' => 4000,
                'status' => 'active',

            ],
            [
                'tld' => '.net.rw',
                'register_price' => 12000,
                'renew_price' => 13000,
                'transfer_price' => 0,
                'grace' => 30,
                'redemption_price' => 4000,
                'status' => 'active',

            ],
            [
                'tld' => '.coop.rw',
                'register_price' => 12000,
                'renew_price' => 13000,
                'transfer_price' => 0,
                'grace' => 30,
                'redemption_price' => 4000,
                'status' => 'active',
            ],
        ];
        foreach ($tlds as $tld) {
            DomainPricing::create($tld);
        }
    }
}
