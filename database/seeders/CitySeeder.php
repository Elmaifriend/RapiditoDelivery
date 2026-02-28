<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        City::create([
            'name' => 'Ciudad de México',
            'state' => 'CDMX',
            'country' => 'Mexico',
            'active' => true,
        ]);

        City::create([
            'name' => 'Querétaro',
            'state' => 'Querétaro',
            'country' => 'Mexico',
            'active' => true,
        ]);
    }
}