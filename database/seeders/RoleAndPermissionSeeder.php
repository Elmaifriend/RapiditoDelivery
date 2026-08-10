<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Resetear la caché de roles y permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Crear o recuperar el rol 'admin' (Garantiza idempotencia)
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
        ]);

        // 3. Correos a los que se les asignará el rol de 'admin'
        $adminEmails = [
            'felipe@rapidito.mx',
            'javier@rapidito.mx',
        ];

        // 4. Buscar los usuarios existentes y asignarles el rol
        foreach ($adminEmails as $email) {
            $user = User::where('email', $email)->first();

            if ($user) {
                // assignRole es idempotente por defecto en Spatie; no duplicará la relación
                $user->assignRole($adminRole);
            }
        }
    }
}