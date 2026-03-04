<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\ProductCategory;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $baseProducts = [
            ['name' => 'Combo Individual', 'price' => 89.00],
            ['name' => 'Combo Familiar', 'price' => 249.00],
            ['name' => 'Producto Especial de la Casa', 'price' => 129.00],
            ['name' => 'Orden Extra', 'price' => 45.00],
            ['name' => 'Bebida 600ml', 'price' => 25.00],
            ['name' => 'Bebida 2L', 'price' => 45.00],
            ['name' => 'Postre del Día', 'price' => 39.00],
            ['name' => 'Promoción 2x1', 'price' => 99.00],
            ['name' => 'Paquete Premium', 'price' => 199.00],
            ['name' => 'Producto Económico', 'price' => 59.00],
        ];

        $restaurants = Restaurant::with('productCategories')->get();

        foreach ($restaurants as $restaurant) {

            // Si el restaurante no tiene categorías, saltamos
            if ($restaurant->productCategories->isEmpty()) {
                continue;
            }

            foreach ($baseProducts as $index => $product) {

                $category = $restaurant->productCategories->random();

                Product::create([
                    'restaurant_id' => $restaurant->id,
                    'product_category_id' => $category->id,
                    'name' => $product['name'],
                    'description' => 'Producto disponible en ' . $restaurant->name,
                    'price' => $product['price'],
                    'is_active' => true,
                    'is_available' => true,
                    'image_path' => null,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }
}