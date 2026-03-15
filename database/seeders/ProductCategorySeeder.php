<?php

namespace Database\Seeders;

use App\Models\Business;
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

        $restaurants = Business::all();

        foreach ($restaurants as $restaurant) {

            foreach ($defaultCategories as $index => $name) {

                ProductCategory::create([
                    'business_id' => $restaurant->id,
                    'name' => $name,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }
}