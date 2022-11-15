<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            Skeleton::class,
            User::class,
            CountrySeeder::class,
            AddressSeeder::class,
            FileSeeder::class,
            RowStatusSeeder::class,
        ]);

        if (env('APP_ENV') === 'local') {
            $this->call([
                CustomerSeeder::class,
            ]);
        }
    }
}
