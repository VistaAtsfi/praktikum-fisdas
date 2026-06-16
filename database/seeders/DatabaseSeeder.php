<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            InitialDataSeeder::class,
            MateriFisdas2025Seeder::class,
            FisdasRealDataSeeder::class,
        ]);
    }
}