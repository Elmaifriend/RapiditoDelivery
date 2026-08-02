<?php

namespace App\Filament\Resources\Drivers\Pages;

use App\Enums\CountryCode;
use App\Filament\Resources\Drivers\DriverResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateDriver extends CreateRecord
{
    protected static string $resource = DriverResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Si se eligió crear un usuario nuevo
        if (! empty($data['create_new_user']) && ! empty($data['new_user'])) {
            $userData = $data['new_user'];

            // Formatear teléfono en formato internacional E.164
            $fullPhone = null;

            if (! empty($userData['phone'])) {
                $rawCountryCode = $userData['country_code'] ?? CountryCode::MX->value;
                $dialCode = CountryCode::tryFrom($rawCountryCode)?->dialCode() ?? '+52';

                // Limpiar espacios, guiones, paréntesis y ceros iniciales
                $cleanNumber = preg_replace('/[^0-9]/', '', $userData['phone']);
                $cleanNumber = ltrim($cleanNumber, '0');

                $fullPhone = $dialCode . $cleanNumber;
            }

            // Crear el nuevo usuario
            $user = User::create([
                'name'     => $userData['name'],
                'email'    => $userData['email'],
                'phone'    => $fullPhone,
                'password' => Hash::make($userData['password']),
            ]);

            // Asignar el ID generado
            $data['user_id'] = $user->id;
        }

        // 2. Limpiar variables temporales para evitar errores SQL en la tabla 'drivers'
        unset($data['create_new_user'], $data['new_user']);

        return $data;
    }
}