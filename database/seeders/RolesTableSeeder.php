<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

final class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'id' => 1,
                'title' => 'Admin',
            ],
            [
                'id' => 2,
                'title' => 'User',
            ],
        ];

        Role::insert($roles);
    }
}
