<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\SubscriptionStatus;
use App\Models\SubscriptionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Number>
 */
class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'registration_date' => now(),
            'subscription_status_id' => SubscriptionStatus::ACTIVE,
            'subscription_type_id' => SubscriptionType::PREPAID,
        ];
    }
}
