<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'id' => 1,
                'user_id' => 1,
                'uuid' => Str::uuid(),
                'contact_id' => 'admin-70vhcHZv',
                'contact_type' => 'admin',
                'name' => 'Jean Paul TURIKUMWE',
                'organization' => 'BLUHUB',
                'street1' => 'KN 20 Ave Nyakabanda',
                'street2' => null,
                'city' => 'Kigali Kicukiro',
                'province' => 'Kigali',
                'postal_code' => '00000',
                'country_code' => 'RW',
                'voice' => '0785446262',
                'fax_number' => null,
                'fax_ext' => null,
                'email' => 'mbabazijacques@gmail.com',
                'auth_info' => null,
                'epp_status' => 'active',
                'created_at' => '2025-03-03 15:25:48',
                'updated_at' => '2025-03-11 07:04:22',
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'uuid' => Str::uuid(),
                'contact_id' => 'registrant-zEcBppi2',
                'contact_type' => 'registrant',
                'name' => 'Jacques MBABAZI',
                'organization' => 'Developer',
                'street1' => 'KN 20 Ave Nyakabanda',
                'street2' => null,
                'city' => 'Kigali Nyarugenge',
                'province' => 'Kigali',
                'postal_code' => '00000',
                'country_code' => 'RW',
                'voice' => '0785446262',
                'fax_number' => null,
                'fax_ext' => null,
                'email' => 'bluhub@gmail.com',
                'auth_info' => null,
                'epp_status' => 'active',
                'created_at' => '2025-03-03 15:34:29',
                'updated_at' => '2025-03-10 14:59:45',
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'uuid' => Str::uuid(),
                'contact_id' => 'tech-VKJzV3mI',
                'contact_type' => 'tech',
                'name' => 'Jacques MBABAZI',
                'organization' => 'BLUHUB',
                'street1' => 'KN 20 Ave Nyakabanda',
                'street2' => null,
                'city' => 'Kigali city',
                'province' => 'Kigali',
                'postal_code' => '00000',
                'country_code' => 'RW',
                'voice' => '0785446262',
                'fax_number' => null,
                'fax_ext' => null,
                'email' => 'mbabazijacques@gmail.com',
                'auth_info' => null,
                'epp_status' => 'active',
                'created_at' => '2025-03-06 20:58:03',
                'updated_at' => '2025-03-06 20:58:03',
            ],
            [
                'id' => 4,
                'user_id' => 1,
                'uuid' => Str::uuid(),
                'contact_id' => 'bill-VKJzV35U',
                'contact_type' => 'billing',
                'name' => 'Jacques MBABAZI',
                'organization' => 'BLUHUB',
                'street1' => 'KN 20 Ave Nyakabanda',
                'street2' => null,
                'city' => 'Kigali city',
                'province' => 'Kigali',
                'postal_code' => '00000',
                'country_code' => 'RW',
                'voice' => '0785446262',
                'fax_number' => null,
                'fax_ext' => null,
                'email' => 'mbabazijacques@gmail.com',
                'auth_info' => null,
                'epp_status' => 'active',
                'created_at' => '2025-03-06 20:58:03',
                'updated_at' => '2025-03-06 20:58:03',

            ],
        ];

        Contact::insert($contacts);
    }
}
