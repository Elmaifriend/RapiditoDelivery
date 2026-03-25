<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryAddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'label' => $this->faker->randomElement(['Casa', 'Trabajo', 'Otro']),
            'formatted_address' => $this->faker->address(),

            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'country' => $this->faker->country(),

            'is_default' => null,
            'source' => null,
            'address_line' => null,
            'reference' => null,
            'photo_path' => null,

            'lat' => $this->faker->latitude(),
            'lng' => $this->faker->longitude(),

            'place_id' => $this->faker->uuid(),
        ];
    }
}