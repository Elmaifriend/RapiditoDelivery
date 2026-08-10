<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Felipe',
                'email' => 'felipe@rapidito.mx',
                'password' => Hash::make('admin'),
            ],
            [
                'name' => 'Javier',
                'email' => 'javier@rapidito.mx',
                'password' => Hash::make('admin'),
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}