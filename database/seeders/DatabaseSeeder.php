<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndMenuSeeder::class,
            RegionSeeder::class,
            LogisticsSeeder::class,
            TariffSeeder::class,
            AccountingSeeder::class
        ]);
    }
}
