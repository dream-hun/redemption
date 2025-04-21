<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class DomainContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'id' => 1,
                'domain_id' => 1,
                'contact_id' => 1,
                'type' => 'admin',
                'user_id' => 1,
            ],
            [
                'id' => 2,
                'domain_id' => 1,
                'contact_id' => 2,
                'type' => 'registrant',
                'user_id' => 1,
            ],
            [
                'id' => 3,
                'domain_id' => 1,
                'contact_id' => 3,
                'type' => 'tech',
                'user_id' => 1,
            ],
            [
                'id' => 4,
                'domain_id' => 1,
                'contact_id' => 4,
                'type' => 'billing',
                'user_id' => 1,
            ],

        ];

        DB::table('domain_contacts')->insert($contacts);
    }
}
