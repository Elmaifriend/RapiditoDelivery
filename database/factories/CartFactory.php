<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 50, 500);
        $delivery = $this->faker->randomFloat(2, 10, 50);

        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'guest_token' => null,

            'restaurant_id' => Restaurant::inRandomOrder()->value('id'),

            'subtotal' => $subtotal,
            'delivery_fee' => $delivery,
            'total' => $subtotal + $delivery,

            'status' => 'active',
            'expires_at' => now()->addHours(2),
        ];
    }

    public function guest()
    {
        return $this->state(function () {
            return [
                'user_id' => null,
                'guest_token' => Str::uuid(),
            ];
        });
    }
}
