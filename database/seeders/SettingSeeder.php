<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

final class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'email' => 'support@bluhub.rw',
            'phone' => '+250 788 683 036',
            'address' => 'KN 12 St, Kigali Rwanda',
            'twitter' => 'https://x.com/bluhub_rw',
            'instagram' => 'https://instagram.com/bluhub_rw',
            'youtube' => 'https://youtube.com/c/bluhub_rw',
            'linkedin' => 'https://linkedin.com/company/bluhub_rw',
        ]);
    }
}
