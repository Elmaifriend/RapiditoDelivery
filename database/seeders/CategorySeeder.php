<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Tacos', 'order' => 1],
            ['name' => 'Pizza', 'order' => 2],
            ['name' => 'Hamburguesas', 'order' => 3],
            ['name' => 'Sushi', 'order' => 4],
            ['name' => 'Café', 'order' => 5],
            ['name' => 'Postres', 'order' => 6],
            ['name' => 'Mariscos', 'order' => 7],
            ['name' => 'Desayunos', 'order' => 8],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'order' => $category['order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
