<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\City;
use Illuminate\Support\Str;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $baseRestaurants = [
            'Pollería El Buen Pollo',
            'OXXO Centro',
            '7-Eleven Reforma',
            'Farmacias Guadalajara',
            'Taquería Los Primos',
            'Sushi Go Express',
            'Pizzería Napoli',
            'Hamburguesas El Jefe',
            'Panadería La Espiga',
            'Super Abarrotes Martínez',
        ];

        $cities = City::all();

        foreach ($cities as $city) {

            foreach ($baseRestaurants as $name) {

                Restaurant::create([
                    'name' => $name,
                    'slug' => Str::slug($name . '-' . $city->name),
                    'status' => 'active',
                    'city_id' => $city->id,
                    'country' => $city->country ?? 'MX',
                    'state' => $city->state ?? null,
                    'address' => 'Dirección ejemplo 123',
                    'lat' => fake()->latitude(14.5, 32.7),
                    'lng' => fake()->longitude(-118.4, -86.7),
                    'is_open' => true,
                    'accepts_delivery' => true,
                    'accepts_pickup' => true,
                ]);
            }
        }
    }
}