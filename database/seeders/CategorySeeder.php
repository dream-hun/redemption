<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'uuid' => Str::uuid(),
                'name' => 'Shared Hosting',
                'slug' => 'shared-hosting',
                'status' => 'active',
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'VPS High Storage',
                'slug' => 'vps-high-storage',
                'status' => 'active',
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'VPS High Performance',
                'slug' => 'vps-high-performance',
                'status' => 'active',
            ],
        ];
        Category::insert($categories);
    }
}
