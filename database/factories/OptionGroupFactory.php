<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OptionGroup;
use App\Models\Product;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OptionGroup>
 */
class OptionGroupFactory extends Factory
{
    protected $model = OptionGroup::class;

    public function definition()
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id,
            'name' => fake()->word(),
            'min' => 0,
            'max' => 3,
            'required' => false,
            'position' => 0,
        ];
    }
}

