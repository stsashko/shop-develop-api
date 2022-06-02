<?php

namespace Database\Factories;

use App\Models\Customers;
use App\Models\Stores;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchasesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'customer_id' => Customers::get()->random()->id,
            'store_id' => Stores::get()->random()->id,
            'purchase_date' => $this->faker->dateTimeBetween('-2 years', 'now', 'Europe/Kiev'),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now', 'Europe/Kiev'),
            'updated_at' => $this->faker->dateTimeBetween('-1 years', 'now', 'Europe/Kiev')
        ];
    }
}
