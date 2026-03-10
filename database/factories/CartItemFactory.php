<?php

namespace Database\Factories;

use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        $quantity = $this->faker->numberBetween(1, 5);

        return [
            'cart_id' => Cart::inRandomOrder()->value('id'),
            'product_id' => $product->id,

            'product_name_snapshot' => $product->name,
            'product_description_snapshot' => $product->description,
            'product_image_url_snapshot' => $product->image,
            'price_snapshot' => $product->price,

            'quantity' => $quantity,
            'subtotal' => $product->price * $quantity,
        ];
    }
}