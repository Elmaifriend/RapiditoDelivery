<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantUserSeeder extends Seeder
{
    public function run()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $user->update([
            'current_restaurant_id' => $restaurant->id,
        ]);
    }
}


