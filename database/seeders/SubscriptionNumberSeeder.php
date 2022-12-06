<?php

namespace Database\Seeders;

use App\Models\SubscriptionNumber;
use Illuminate\Database\Seeder;

class SubscriptionNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $insert = [];
//        for ($i = 1; $i < 145; $i++) {
//            $insert[] = [
//                'subscription_id' => $i,
//                'number_id' => $i,
//                'imsi_id' => $i,
//                'activation_date' => now(),
//            ];
//        }

        SubscriptionNumber::factory()->count(150)->create();
    }
}
