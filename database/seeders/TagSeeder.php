<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run()
    {
        $tags = [
            'Tacos',
            'Pizza',
            'Hamburguesas',
            'Sushi',
            'Vegano',
            'Postres',
            'Café',
            'Desayunos',
            'Mariscos',
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate([
                'name' => $tag
            ]);
        }
    }
}

