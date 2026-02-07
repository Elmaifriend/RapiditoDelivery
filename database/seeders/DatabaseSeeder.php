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
        User::firstOrCreate([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            "password" => "admin"
        ]);

        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
            RestaurantSeeder::class, 
            ProductSeeder::class,
            OptionGroupSeeder::class,
        ]);
    }
}
