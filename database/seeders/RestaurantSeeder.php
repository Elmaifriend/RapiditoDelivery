<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\Tag;


class RestaurantSeeder extends Seeder
{
    public function run()
    {
        $restaurant = Restaurant::factory()
            ->count(10)
            ->create()
            ->each(fn ($restaurant) =>
                $restaurant->tags()->attach(
                    Tag::inRandomOrder()->limit(rand(3, 8))->pluck('id')
                )
            );
    }
}


