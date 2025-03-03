<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id' => 1,
                'uuid' => Str::uuid(),
                'client_code' => 'BLCL-000001',
                'name' => 'MBABAZI Jacques',
                'email' => 'mbabazijacques@gmail.com',
                'password' => bcrypt('password'),
                'remember_token' => null,
            ],
        ];

        User::insert($users);
    }
}
