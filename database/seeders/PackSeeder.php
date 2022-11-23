<?php

namespace Database\Seeders;

use App\Models\Pack;
use Illuminate\Database\Seeder;

class PackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Pack::factory()->count(10)
                ->create(['product_id' => 1, 'pack_type_id' => 1]);
    }
}
