<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EquipementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom_equipement' => $this->faker->word(),
            'icon' => $this->faker->randomElement(['wifi', 'kitchen', 'tv', 'ac', 'pool', 'parking']),
        ];
    }
}
