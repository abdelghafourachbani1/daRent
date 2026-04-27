<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom_ville' => $this->faker->city(),
            'region' => $this->faker->state(),
        ];
    }
}
