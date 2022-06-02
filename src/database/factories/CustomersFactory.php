<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'customer_fname' => $this->faker->firstName,
            'customer_lname' => $this->faker->lastName,
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now', 'Europe/Kiev'),
            'updated_at' => $this->faker->dateTimeBetween('-1 years', 'now', 'Europe/Kiev')
        ];
    }
}
