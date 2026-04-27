<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'property_id' => Property::factory(),
            'note' => $this->faker->numberBetween(1, 5),
            'commentaire' => $this->faker->paragraph(),
            'date_publication' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
