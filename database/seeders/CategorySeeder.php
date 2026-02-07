<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Taquería'],
            ['name' => 'Hamburguesería'],
            ['name' => 'Pizzería'],
            ['name' => 'Sushi Bar'],
            ['name' => 'Cafetería'],
            ['name' => 'Marisquería'],
            ['name' => 'Restaurante Mexicano'],
            ['name' => 'Restaurante Italiano'],
            ['name' => 'Comida China'],
            ['name' => 'Comida Rápida'],
            ['name' => 'Panadería'],
            ['name' => 'Pastelería'],
            ['name' => 'Desayunos'],
            ['name' => 'Lonchería'],
            ['name' => 'Pollería'],
            ['name' => 'Parrilla / Asador'],
            ['name' => 'Heladería'],
            ['name' => 'Jugos y Licuados'],
            ['name' => 'Farmacia'],
            ['name' => 'Tienda de Abarrotes'],
            ['name' => 'Mini Súper'],
            ['name' => 'Carnicería'],
            ['name' => 'Verdulería'],
            ['name' => 'Tortillería'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                [
                    'name' => $category['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
