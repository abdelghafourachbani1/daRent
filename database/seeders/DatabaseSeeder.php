<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Equipement;
use App\Models\Property;
use App\Models\reservation;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create 5 cities
        City::factory(5)->create();

        // Create 8 categories
        Category::factory(8)->create();

        // Create 10 users
        User::factory(10)->create();

        // Create 20 properties
        Property::factory(20)->create();

        // Create 15 equipements
        Equipement::factory(15)->create();

        // Create 30 reviews
        Review::factory(30)->create();

        // Create 25 reservations
        reservation::factory(25)->create();
    }
}
