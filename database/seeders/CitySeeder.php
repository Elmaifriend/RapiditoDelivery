<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        City::create([
            'name' => 'Puerto Peñasco',
            'state' => 'Sonora',
            'country' => 'Mexico',
            'active' => true,
        ]);
    }
}