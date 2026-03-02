<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        City::create([
            'name' => 'Tijuana',
            'state' => 'Baja California',
            'country' => 'Mexico',
            'active' => true,
        ]);

        City::create([
            'name' => 'Tecate',
            'state' => 'Baja California',
            'country' => 'Mexico',
            'active' => true,
        ]);

        City::create([
            'name' => 'Puerto Peñasco',
            'state' => 'Sonora',
            'country' => 'Mexico',
            'active' => true,
        ]);
    }
}