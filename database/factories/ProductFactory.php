<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'business_id' => fn () => Business::inRandomOrder()->first()?->id ?? Business::factory()->create()->id,
            'product_category_id' => fn () => ProductCategory::inRandomOrder()->first()?->id ?? ProductCategory::factory()->create()->id,
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 5, 100),
            'is_active' => fake()->boolean(80),
            'is_available' => fake()->boolean(90),
            'image_path' => null,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
