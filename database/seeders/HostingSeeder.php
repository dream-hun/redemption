<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hosting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class HostingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'category_id' => 1,
                'name' => 'Bronze',
                'slug' => Str::slug('Bronze'),
                'icon' => 'https://cdn-icons-png.freepik.com/512/622/622339.png?fd=1&filename=servers_622339.png',
                'status' => 'active',
                'price' => 40000,
                'period' => 1,
            ],
            [
                'category_id' => 1,
                'name' => 'White',
                'slug' => Str::slug('White'),
                'icon' => 'https://cdn-icons-png.freepik.com/512/622/622339.png?fd=1&filename=servers_622339.png',
                'status' => 'active',
                'price' => 65000,
                'period' => 1,

            ],
            [
                'category_id' => 1,
                'name' => 'Gold',
                'slug' => Str::slug('Gold'),
                'icon' => 'https://cdn-icons-png.freepik.com/512/622/622339.png?fd=1&filename=servers_622339.png',
                'status' => 'active',
                'price' => 100000,
                'period' => 1,
            ],
            [
                'category_id' => 1,
                'name' => 'Gold Premium',
                'slug' => Str::slug('Gold Premium'),
                'icon' => 'https://cdn-icons-png.freepik.com/512/622/622339.png?fd=1&filename=servers_622339.png',
                'status' => 'active',
                'price' => 250000,
                'period' => 1,
            ],

        ];

        Hosting::insert($plans);
    }
}
