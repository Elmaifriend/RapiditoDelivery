<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderDropoffLocation;

class OrderDropoffLocationSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            OrderDropoffLocation::create([
                'order_id' => $order->id,
                'lat' => fake()->latitude(),
                'lng' => fake()->longitude(),
                'source' => 'user',
                'confirmed' => true,
            ]);
        }
    }
}