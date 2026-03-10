<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $product = Product::inRandomOrder()->first() ?? Product::factory()->create();

        $quantity = $this->faker->numberBetween(1, 4);

        $price = $product->price ?? $this->faker->randomFloat(2, 50, 200);

        return [

            'order_id' => Order::factory(),

            'product_id' => $product->id,

            'product_name_snapshot' => $product->name ?? $this->faker->words(3, true),

            'product_description_snapshot' => $product->description ?? $this->faker->sentence(),

            'product_image_url_snapshot' => $product->image_url ?? $this->faker->imageUrl(),

            'price_snapshot' => $price,

            'quantity' => $quantity,

            'subtotal' => $price * $quantity,
        ];
    }
}