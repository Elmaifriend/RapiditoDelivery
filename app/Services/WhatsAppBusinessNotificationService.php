<?php

namespace App\Services;

use App\Models\Business;
use App\Models\User;
use App\WhatsApp\WhatsApp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class WhatsAppBusinessNotificationService
{
    /**
     * Envía un mensaje de bienvenida a cada usuario responsable del restaurante.
     */
    public function sendWelcomeMessage(Business $business): bool
    {
        // Obtener los usuarios asociados al negocio
        $users = $business->users;

        if ($users->isEmpty()) {
            $users = User::where('business_id', $business->id)
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->get();
        }

        if ($users->isEmpty()) {
            Log::warning("No se encontraron usuarios vinculados con teléfono para el negocio: {$business->name} (ID: {$business->id})");
            return false;
        }

        // Generar URL pública del catálogo en línea
        $catalogUrl = route('business', ['business' => $business->id]);

        // Generar URL firmada para la gestión de órdenes en cocina (expira en 30 días)
        $ordersUrl = URL::temporarySignedRoute(
            'kitchen.orders',
            now()->addDays(30),
            ['businessId' => $business->id]
        );

        $allSentSuccessfully = true;

        foreach ($users as $user) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $user->phone ?? '');

            if (empty($cleanPhone)) {
                Log::warning("El usuario {$user->name} (ID: {$user->id}) no cuenta con un número de teléfono válido.");
                $allSentSuccessfully = false;
                continue;
            }

            $params = [
                'user_name'     => $user->name,
                'business_name' => $business->name,
                'catalog_url'   => $catalogUrl,
                'orders_url'    => $ordersUrl,
            ];

            try {
                Log::info("Enviando bienvenida vía WhatsApp a: {$user->name} ({$cleanPhone}) de {$business->name}");

                $sent = WhatsApp::sendTemplate($cleanPhone, 'welcome_business', $params);

                if (!$sent) {
                    $allSentSuccessfully = false;
                    Log::error("Meta rechazó el envío de bienvenida al usuario {$user->name} ({$cleanPhone})");
                }
            } catch (\Exception $e) {
                $allSentSuccessfully = false;
                Log::error("Error al enviar bienvenida a {$user->name}: " . $e->getMessage());
            }
        }

        return $allSentSuccessfully;
    }
}