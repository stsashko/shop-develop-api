<?php

namespace Database\Factories;

use App\Models\Products;
use App\Models\Purchases;
use Illuminate\Database\Eloquent\Factories\Factory;

class Purchase_itemsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'purchase_id' => Purchases::get()->random()->id,
            'product_id' => Products::get()->random()->id,
            'product_count' => mt_rand(5, 100),
            'product_price' => $this->faker->randomFloat(2, 10, 300),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now', 'Europe/Kiev'),
            'updated_at' => $this->faker->dateTimeBetween('-1 years', 'now', 'Europe/Kiev')
        ];
    }
}
