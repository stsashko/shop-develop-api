<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StoresFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'store_name' => 'Branch - ' . $this->faker->country,
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now', 'Europe/Kiev'),
            'updated_at' => $this->faker->dateTimeBetween('-1 years', 'now', 'Europe/Kiev')
        ];
    }
}
