<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::inRandomOrder()->first()->id,
            'name' => fake()->randomElement([
                'Entradas',
                'Combos',
                'Bebidas',
                'Postres',
                'Especialidades',
            ]),
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}