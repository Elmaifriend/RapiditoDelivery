<?php

namespace Database\Factories;

use App\Models\OrderDropoffLocation;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDropoffLocationFactory extends Factory
{
    protected $model = OrderDropoffLocation::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'formatted_address' => fake()->address(),
            'lat' => $this->faker->latitude(),
            'lng' => $this->faker->longitude(),
            'source' => 'user',
            'confirmed' => true,
        ];
    }
}