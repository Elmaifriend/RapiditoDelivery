<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use App\Models\Restaurant;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $defaultCategories = [
            'Entradas',
            'Platos Fuertes',
            'Bebidas',
            'Postres',
        ];

        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {

            foreach ($defaultCategories as $index => $name) {

                ProductCategory::create([
                    'restaurant_id' => $restaurant->id,
                    'name' => $name,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }
}