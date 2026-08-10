<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RoleAndPermissionSeeder::class,
            CitySeeder::class,
            //CategorySeeder::class,
            TagSeeder::class,
            //BusinessSeeder::class, 
            //ProductCategorySeeder::class,
            //ProductSeeder::class,
            //OptionGroupSeeder::class,
            ServiceZoneSeeder::class,
            DeliveryZoneSeeder::class,
            DeliveryZoneFareSeeder::class,
            //DeliveryAddressSeeder::class,
            //CartSeeder::class,
            //CartItemSeeder::class,
            //OrderSeeder::class,
            //OrderItemSeeder::class,
            //OrderDropoffLocationSeeder::class,
            //ScheduleSeeder::class,
        ]);
    }
}
