<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();

        foreach ($orders as $order) {

            $products = Product::inRandomOrder()
                ->take(rand(1, 5))
                ->get();

            foreach ($products as $product) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => rand(1, 3),
                    'price' => $product->price,
                ]);
            }
        }
    }
}