<?php

namespace Database\Factories;

use App\Models\Categories;
use App\Models\Manufacturers;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_name' => ucfirst($this->faker->company) . (' №' . mt_rand(100, 999999)),
            'image' => $this->faker->imageUrl(500, 500),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'category_id' => Categories::get()->random()->id,
            'manufacturer_id' => Manufacturers::get()->random()->id,
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now', 'Europe/Kiev'),
            'updated_at' => $this->faker->dateTimeBetween('-1 years', 'now', 'Europe/Kiev')
        ];
    }
}
