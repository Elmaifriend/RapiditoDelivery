<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\City;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class RestaurantSeeder extends Seeder
{
    protected string $disk;

    public function __construct()
    {
        // Usa el disk configurado en FILESYSTEM_DISK
        $this->disk = config('filesystems.default');
    }

    public function run(): void
    {
        $basePath = database_path('seeders/files/restaurants');

        $folders = [
            'banner' => $basePath . '/banner',
            'logo' => $basePath . '/logo',
            'reference' => $basePath . '/referencia',
        ];

        $storedFiles = [];

        foreach ($folders as $key => $path) {

            Storage::disk($this->disk)->makeDirectory("restaurants/{$key}");

            $storedFiles[$key] = $this->copyFiles(
                $path,
                "restaurants/{$key}"
            );
        }

        $baseRestaurants = [
            'Pollería El Buen Pollo',
            'OXXO Centro',
            '7-Eleven Reforma',
            'Farmacias Guadalajara',
            'Taquería Los Primos',
            'Sushi Go Express',
            'Pizzería Napoli',
            'Hamburguesas El Jefe',
            'Panadería La Espiga',
            'Super Abarrotes Martínez',
        ];

        $cities = City::all();

        foreach ($cities as $city) {

            foreach ($baseRestaurants as $name) {

                $slug = Str::slug($name);

                Restaurant::create([
                    'name' => $name,
                    'slug' => Str::slug($name . '-' . $city->name),
                    'status' => 'active',
                    'city_id' => $city->id,
                    'country' => $city->country ?? 'MX',
                    'state' => $city->state ?? null,
                    'address' => 'Dirección ejemplo 123',
                    'lat' => fake()->latitude(14.5, 32.7),
                    'lng' => fake()->longitude(-118.4, -86.7),
                    'is_open' => true,
                    'accepts_delivery' => true,
                    'accepts_pickup' => true,
                    'banner_path' => $this->matchImage($slug, $storedFiles['banner']),
                    'logo_path' => $this->matchImage($slug, $storedFiles['logo']),
                    'reference_image' => $this->matchImage($slug, $storedFiles['reference']),
                ]);
            }
        }
    }

    private function copyFiles(string $sourcePath, string $destinationFolder): array
    {
        $files = File::files($sourcePath);
        $stored = [];

        foreach ($files as $file) {

            $fileName = $file->getFilename();
            $destination = $destinationFolder . '/' . $fileName;

            Storage::disk($this->disk)->put(
                $destination,
                File::get($file->getRealPath())
            );

            $stored[] = $destination;
        }

        return $stored;
    }

    private function matchImage(string $slug, array $files): string
    {
        foreach ($files as $file) {

            if (Str::contains($file, 'oxxo') && Str::contains($slug, 'oxxo')) return $file;
            if (Str::contains($file, '7eleven') && Str::contains($slug, '7-eleven')) return $file;
            if (Str::contains($file, 'polleria') && Str::contains($slug, 'polleria')) return $file;
            if (Str::contains($file, 'farmacia') && Str::contains($slug, 'farmacias')) return $file;
            if (Str::contains($file, 'taqueria') && Str::contains($slug, 'taqueria')) return $file;
        }

        return $files[array_rand($files)];
    }
}