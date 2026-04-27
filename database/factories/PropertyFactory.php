<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'city_id' => City::factory(),
            'category_id' => Category::factory(),
            'titre' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'adress' => $this->faker->address(),
            'prix_mensuel' => $this->faker->numberBetween(500, 5000),
            'type' => $this->faker->randomElement(['apartment', 'house', 'villa', 'room']),
            'status' => 'available',
            'bedrooms' => $this->faker->numberBetween(1, 5),
            'bathrooms' => $this->faker->numberBetween(1, 3),
            'availability' => true,
        ];
    }
}
