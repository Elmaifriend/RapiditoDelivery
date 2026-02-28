<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\OrderStatus;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 500);
        $deliveryFee = $this->faker->randomFloat(2, 10, 50);

        return [
            'user_id' => User::factory(),
            'restaurant_id' => Restaurant::factory(),
            'driver_id' => null,
            'status' => OrderStatus::REFUND_PENDING,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $subtotal + $deliveryFee,
            'payment_status' => OrderStatus::CONFIRMED,
        ];
    }
}