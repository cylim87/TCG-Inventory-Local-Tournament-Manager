<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SupplierSeeder::class,
            CardSetSeeder::class,
            ProductSeeder::class,
            PlayerSeeder::class,
            TournamentSeeder::class,
        ]);
    }
}
