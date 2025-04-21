<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            HostingSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionRoleTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
            CountrySeeder::class,
            DomainPricingSeeder::class,
            SettingSeeder::class,
            ContactSeeder::class,
            DomainSeeder::class,
            DomainContactSeeder::class,
            NameserverSeeder::class,
        ]);
    }
}
