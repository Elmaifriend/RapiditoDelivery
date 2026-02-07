<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition(): array
    {
        $name = $this->faker->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),

            'status' => $this->faker->randomElement(['active', 'inactive']),
            'category_id' => Category::inRandomOrder()->first()->id,

            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => 'MX',

            // Coordenadas aprox México
            'lat' => $this->faker->latitude(14.5, 32.7),
            'lng' => $this->faker->longitude(-118.4, -86.7),

            'google_maps_url' => null,

            'logo_path' => null,
            'banner_path' => null,
            'reference_image' => null,

            'is_open' => $this->faker->boolean(80),
            'accepts_delivery' => $this->faker->boolean(70),
            'accepts_pickup' => $this->faker->boolean(90),
        ];
    }

    /* ======================
     | Estados útiles
     ====================== */

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => 'active',
            'is_open' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'status' => 'inactive',
            'is_open' => false,
        ]);
    }

    public function deliveryOnly(): static
    {
        return $this->state(fn () => [
            'accepts_delivery' => true,
            'accepts_pickup' => false,
        ]);
    }

    public function pickupOnly(): static
    {
        return $this->state(fn () => [
            'accepts_delivery' => false,
            'accepts_pickup' => true,
        ]);
    }
}
