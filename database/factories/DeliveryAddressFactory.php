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

            'street' => $this->faker->streetName(),
            'street_number' => $this->faker->buildingNumber(),
            'neighborhood' => $this->faker->citySuffix(),

            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->country(),

            'lat' => $this->faker->latitude(),
            'lng' => $this->faker->longitude(),

            'place_id' => $this->faker->uuid(),
        ];
    }
}