<?php

namespace App\Filament\Resources\Drivers\Pages;

use App\Filament\Resources\Drivers\DriverResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateDriver extends CreateRecord
{
    protected static string $resource = DriverResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Si se eligió crear un usuario nuevo y existen sus datos
        if (!empty($data['new_user']) && !empty($data['new_user']['email'])) {
            $userData = $data['new_user'];

            $user = User::create([
                'name'     => $userData['name'],
                'email'    => $userData['email'],
                'phone'    => $userData['phone'] ?? null,
                'password' => Hash::make($userData['password']),
            ]);

            $data['user_id'] = $user->id;
        }

        // 2. Limpiar llaves secundarias/virtuales
        unset($data['create_new_user'], $data['new_user']);

        return $data;
    }
}