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
            // Comida principal
            'Tacos',
            'Hamburguesas',
            'Pizza',
            'Sushi',
            'Hot Dogs',
            'Alitas',
            'Boneless',
            'Burritos',
            'Quesadillas',
            'Tortas',
            'Papas a la francesa',
            'Nachos',
            'Ensaladas',
            'Sopas',
            'Caldo',
            'Pozole',
            'Tamales',
            'Chilaquiles',
            'Molletes',
            'Pancakes',

            // Especialidades
            'Mariscos',
            'Camarones',
            'Pulpo',
            'Pescado',
            'Carne asada',
            'Parrilladas',
            'Pollo asado',
            'Pollo frito',
            'Costillas',
            'Cortes premium',

            // Opciones alimenticias
            'Vegano',
            'Vegetariano',
            'Sin gluten',
            'Keto',
            'Saludable',
            'Orgánico',
            'Light',

            // Bebidas
            'Café',
            'Café frío',
            'Capuchino',
            'Latte',
            'Té',
            'Té chai',
            'Jugos naturales',
            'Licuados',
            'Smoothies',
            'Malteadas',
            'Refrescos',
            'Agua fresca',

            // Postres y dulces
            'Postres',
            'Pasteles',
            'Pays',
            'Galletas',
            'Brownies',
            'Helado',
            'Nieve',
            'Churros',
            'Pan dulce',
            'Pan artesanal',

            // Horarios / ocasiones
            'Desayunos',
            'Comidas',
            'Cenas',
            '24 horas',
            'Para llevar',
            'Entrega a domicilio',

            // Retail / extra útil
            'Medicamentos',
            'Productos de limpieza',
            'Snacks',
            'Botanas',
            'Bebidas alcohólicas',
        ];


        foreach ($tags as $tag) {
            Tag::firstOrCreate([
                'name' => $tag
            ]);
        }
    }
}

