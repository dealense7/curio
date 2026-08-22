<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Acl\PermissionSeeder;
use Database\Seeders\Acl\RoleSeeder;
use Database\Seeders\General\Category\CategorySeeder;
use Database\Seeders\General\CountrySeeder;
use Database\Seeders\General\CurrencySeeder;
use Database\Seeders\Retailer\RetailerSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            CountrySeeder::class,
            CurrencySeeder::class,
            RetailerSeeder::class,
            CategorySeeder::class,
        ]);
    }
}
