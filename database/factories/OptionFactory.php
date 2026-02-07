<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Option;
use App\Models\OptionGroup;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Option>
 */
class OptionFactory extends Factory
{
    protected $model = Option::class;

    public function definition()
    {
        return [
            'option_group_id' => OptionGroup::inRandomOrder()->first()->id,
            'name' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, 0, 50),
            'is_active' => true,
            'position' => 0,
        ];
    }
}

