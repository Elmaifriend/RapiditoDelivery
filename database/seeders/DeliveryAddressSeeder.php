<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliveryAddress;

class DeliveryAddressSeeder extends Seeder
{
    public function run(): void
    {
        DeliveryAddress::factory()
            ->count(20)
            ->create();
    }
}