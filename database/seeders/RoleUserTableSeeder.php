<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class RoleUserTableSeeder extends Seeder
{
    public function run()
    {
        User::findOrFail(1)->roles()->sync(1);
    }
}
