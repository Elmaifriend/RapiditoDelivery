<?php

namespace App\Services;

use App\Models\Order;
use App\WhatsApp\WhatsApp;
use Illuminate\Support\Facades\Log;

class WhatsAppCustomerNotificationService
{
    /**
     * Notifica al cliente por WhatsApp que su orden está lista.
     */
    public function notifyCustomerOrderIsReady(Order $order): bool
    {
        // Asegurar la carga de la relación con el negocio
        $order->loadMissing('business');

        // Extraer ÚNICAMENTE el customer_name del modelo Order
        $customerName  = $order->customer_name ?? 'Cliente';
        $customerPhone = $order->customer_phone;
        $businessName  = $order->business?->name ?? 'nuestro negocio';

        // Limpieza del número telefónico
        $cleanPhone = preg_replace('/[^0-9]/', '', $customerPhone ?? '');

        if (empty($cleanPhone)) {
            Log::warning("El cliente '{$customerName}' en la Orden ID {$order->id} no cuenta con teléfono válido.");
            return false;
        }

        // Mapeo posicional para la plantilla Meta:
        // {{1}} = customer_name
        // {{2}} = business_name
        $params = [
            'customer_name' => $customerName,
            'business_name' => $businessName,
        ];

        try {
            Log::info("Enviando WhatsApp a cliente: {$customerName} ({$cleanPhone}) de la orden #{$order->id}");

            $sent = WhatsApp::sendTemplate($cleanPhone, 'customer_order_ready', $params);

            if (!$sent) {
                Log::error("Error al enviar WhatsApp a {$customerName} ({$cleanPhone})");
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Excepción al notificar cliente {$customerName}: " . $e->getMessage());
            return false;
        }
    }
}