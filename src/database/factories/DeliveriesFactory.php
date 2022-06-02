<?php

namespace Database\Factories;

use App\Models\Products;
use App\Models\Stores;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_id' => Products::get()->random()->id,
            'store_id' => Stores::get()->random()->id,
            'delivery_date' => $this->faker->dateTimeBetween('-1 years'),
            'product_count' => mt_rand(1, 100),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now', 'Europe/Kiev'),
            'updated_at' => $this->faker->dateTimeBetween('-1 years', 'now', 'Europe/Kiev')
        ];
    }
}
