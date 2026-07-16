<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        return [
            'business_id' => fn () => Business::inRandomOrder()->first()?->id ?? Business::factory()->create()->id,
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
