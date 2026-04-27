<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class reservationFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('+1 day', '+30 days');
        $checkOut = (clone $checkIn)->modify('+' . $this->faker->numberBetween(1, 14) . ' days');
        
        return [
            'property_id' => Property::factory(),
            'tenant_id' => User::factory(),
            'owner_id' => User::factory(),
            'date_debut' => $checkIn,
            'date_fin' => $checkOut,
            'prix_total' => $this->faker->numberBetween(500, 5000),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']),
            'message' => $this->faker->paragraph(),
        ];
    }
}
