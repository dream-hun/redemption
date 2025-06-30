<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id' => 1,
                'uuid' => Str::uuid(),
                'client_code' => 'BLCL-000001',
                'first_name' => 'Jacques',
                'last_name' => 'MBABAZI',
                'email' => 'mbabazijacques@gmail.com',
                'password' => bcrypt('password'),
                'remember_token' => null,
            ],
        ];

        User::insert($users);
    }
}
