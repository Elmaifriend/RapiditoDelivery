<?php

namespace Database\Factories;

use App\Models\ServiceZone;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceZoneFactory extends Factory
{
    protected $model = ServiceZone::class;

    public function definition(): array
    {
        return [
            'city_id' => City::factory(),
            'name' => 'Zona ' . $this->faker->randomElement(['Centro', 'Norte', 'Sur']),
            'polygon' => [
                'type' => 'Polygon',
                'coordinates' => [[
                    [-99.140, 19.430],
                    [-99.150, 19.430],
                    [-99.150, 19.420],
                    [-99.140, 19.420],
                    [-99.140, 19.430],
                ]]
            ],
            'active' => true,
            'debug' => false,
        ];
    }
}