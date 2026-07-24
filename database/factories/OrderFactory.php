<?php

namespace Database\Factories;

use App\Enums\BusinessDecisionStatus;
use App\Enums\DeliveryStatus;
use App\Enums\OrderLifecycleStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Business;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 500);
        $deliveryFee = $this->faker->randomFloat(2, 10, 50);

        return [

            'user_id' => User::factory(),

            'business_id' => Business::inRandomOrder()->first()?->id
                ?? Business::factory(),

            'driver_id' => null,

            'special_instructions' => fake()->paragraph(),

            'lifecycle_status' => OrderLifecycleStatus::CONFIRMED,

            'business_decision_status' => BusinessDecisionStatus::PENDING,

            'delivery_status' => null,

            'payment_status' => PaymentStatus::PENDING,

            'subtotal' => $subtotal,

            'delivery_fee' => $deliveryFee,

            'total' => $subtotal + $deliveryFee,

            'payment_method' => PaymentMethod::CASH->value,
        ];
    }

    public function ready(): static
    {
        return $this->state(fn () => [
            'lifecycle_status' => OrderLifecycleStatus::READY,
            'business_decision_status' => BusinessDecisionStatus::ACCEPTED,
            'delivery_status' => DeliveryStatus::WAITING_DRIVER,
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn () => [
            'lifecycle_status' => OrderLifecycleStatus::DELIVERED,
            'business_decision_status' => BusinessDecisionStatus::ACCEPTED,
            'delivery_status' => DeliveryStatus::DELIVERED,
            'payment_status' => PaymentStatus::PAID,
        ]);
    }
}
