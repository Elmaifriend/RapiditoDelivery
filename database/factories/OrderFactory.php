<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\OrderLifecycleStatus;
use App\Enums\RestaurantDecisionStatus;
use App\Enums\DeliveryStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use Faker\Provider\Payment;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 500);
        $deliveryFee = $this->faker->randomFloat(2, 10, 50);

        return [

            'user_id' => User::factory(),

            'restaurant_id' => Restaurant::inRandomOrder()->first()?->id
                ?? Restaurant::factory(),

            'driver_id' => null,

            'lifecycle_status' => OrderLifecycleStatus::CONFIRMED,

            'restaurant_decision_status' => RestaurantDecisionStatus::PENDING,

            'delivery_status' => null,

            'payment_status' => PaymentStatus::PENDING,

            'subtotal' => $subtotal,

            'delivery_fee' => $deliveryFee,

            'total' => $subtotal + $deliveryFee,

            "payment_method" => PaymentMethod::CASH->value,
        ];
    }

    public function ready(): static
    {
        return $this->state(fn () => [
            'lifecycle_status' => OrderLifecycleStatus::READY,
            'restaurant_decision_status' => RestaurantDecisionStatus::ACCEPTED,
            'delivery_status' => DeliveryStatus::WAITING_DRIVER,
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn () => [
            'lifecycle_status' => OrderLifecycleStatus::DELIVERED,
            'restaurant_decision_status' => RestaurantDecisionStatus::ACCEPTED,
            'delivery_status' => DeliveryStatus::DELIVERED,
            'payment_status' => PaymentStatus::PAID,
        ]);
    }


}