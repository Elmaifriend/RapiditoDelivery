<?php

namespace App\Services;

use App\Models\Order;
use App\WhatsApp\WhatsApp;
use Illuminate\Support\Facades\Log;
use App\Models\Business;

class WhatsAppNotifierService
{
    /**
     * Envía un mensaje al cliente confirmando su orden usando la plantilla registrada.
     */
    public function notifyCustomerOrderCreated(Order $order): bool
    {
        $phone = $order->customer_phone;
        $name = $order->customer_name;
        
        if (!$phone) {
            return false;
        }

        if (!$order->relationLoaded('items')) {
            $order->load('items');
        }

        $itemsList = "";
        foreach ($order->items as $item) {
            $itemsList .= "• {$item->quantity}x {$item->product_name_snapshot}\n";
        }
        $itemsList = rtrim($itemsList);

        $params = [
            'name'         => $name ?? 'Cliente',
            'order_id'     => $order->id,
            'items_list'   => $itemsList,
            'subtotal'     => '$' . number_format($order->subtotal, 2),
            'delivery_fee' => '$' . number_format($order->delivery_fee, 2),
            'total'        => '$' . number_format($order->total, 2),
        ];

        try {
            return WhatsApp::sendTemplate($phone, 'order_processed_customer', $params);
        } catch (\Exception $e) {
            Log::error("Error al enviar WhatsApp al cliente para la orden {$order->id}: " . $e->getMessage());
            return false;
        }
    }
    
    /*
     * Envía un mensaje al restaurante notificando el nuevo pedido usando la plantilla registrada.
     */
    public function notifyRestaurantNewOrder(Order $order): bool
    {
        if (!$order->relationLoaded('business')) {
            $order->load('business');
        }

        $business = $order->business;
        
        if (!$business) {
            Log::warning("No se pudo enviar notificación: la orden {$order->id} no tiene un negocio asociado.");
            return false;
        }

        if (!$order->relationLoaded('items')) {
            $order->load('items');
        }

        // Buscamos usuarios asignados directamente por la columna business_id
        $users = \App\Models\User::where('business_id', $business->id)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        if ($users->isEmpty()) {
            Log::warning("No se encontraron usuarios con teléfono vinculados al business_id para el restaurante: {$business->name}");
            return false;
        }

        // Estructuramos la lista de productos
        $itemsSummary = "";
        foreach ($order->items as $item) {
            $itemsSummary .= "- {$item->quantity}x {$item->product_name_snapshot}\n";
        }
        $itemsSummary = rtrim($itemsSummary);

        if (empty($itemsSummary)) {
            $itemsSummary = "Detalles en la tablet / panel";
        }

        // Generamos dinámicamente la URL absoluta para este negocio usando tu ruta con nombre
        $dashboardUrl = route('kitchen.orders', ['businessId' => $business->id]);

        // Parámetros de la plantilla de Meta (asegúrate de agregar la variable en tu plantilla de Meta si es necesario)
        $params = [
            'restaurant_name'      => $business->name,
            'order_id'             => $order->id,
            'items_summary'        => $itemsSummary,
            'special_instructions' => $order->special_instructions ?? 'Ninguna',
            'dashboard_url'        => $dashboardUrl, 
        ];

        $allSentSuccessfully = true;

        foreach ($users as $user) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $user->phone);

            if (empty($cleanPhone)) {
                Log::error("El usuario {$user->name} (ID: {$user->id}) tiene un teléfono inválido: '{$user->phone}'");
                $allSentSuccessfully = false;
                continue;
            }

            try {
                Log::info("Intentando notificar vía WhatsApp a restaurante: {$business->name} -> Usuario: {$user->name} ({$cleanPhone}) | URL: {$dashboardUrl}");

                $sent = WhatsApp::sendTemplate($cleanPhone, 'new_order_restaurant', $params);
                
                if (!$sent) {
                    $allSentSuccessfully = false;
                    Log::error("Meta rechazó el envío de WhatsApp al usuario {$user->name} ({$cleanPhone}) para la orden {$order->id}");
                }
            } catch (\Exception $e) {
                $allSentSuccessfully = false;
                Log::error("Error al enviar WhatsApp a {$user->name} para la orden {$order->id}: " . $e->getMessage());
            }
        }

        return $allSentSuccessfully;
    }

    public function notifyDriverShiftReminder(\App\Models\Driver $driver, string $startTime): bool
    {
        if (!$driver->relationLoaded('user')) {
            $driver->load('user');
        }

        $user = $driver->user;

        if (!$user || !$user->phone) {
            Log::warning("No se pudo notificar al driver ID {$driver->id}: no tiene un usuario o teléfono asociado.");
            return false;
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $user->phone);

        if (empty($cleanPhone)) {
            Log::error("Teléfono inválido para el repartidor {$user->name} (Driver ID: {$driver->id})");
            return false;
        }

        // Ruta corregida: apunta al perfil del conductor pasando el ID en la clave 'driver'
        $shiftUrl = route('driver.profile', ['driver' => $driver->id]);

        $params = [
            'driver_name' => $user->name,
            'start_time'  => $startTime,
            'shift_url'   => $shiftUrl,
        ];

        try {
            Log::info("Enviando recordatorio de turno a repartidor: {$user->name} ({$cleanPhone}) a las {$startTime}");
            
            return WhatsApp::sendTemplate($cleanPhone, 'driver_shift_reminder', $params);
        } catch (\Exception $e) {
            Log::error("Error al enviar recordatorio de turno por WhatsApp al driver ID {$driver->id}: " . $e->getMessage());
            return false;
        }
    }

    public function notifyBusinessStatusChange(Business $business, string $action): bool
    {
        // Obtener usuarios vinculados al restaurante que tengan teléfono
        $users = $business->users()
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        if ($users->isEmpty()) {
            Log::warning("No hay usuarios registrados con teléfono para notificar el cambio de estado del negocio {$business->name} (ID: {$business->id}).");
            return false;
        }

        $statusLabel = ($action === 'open') ? 'ABIERTO 🟢' : 'CERRADO 🔴';

        // Ruta corregida: apunta a la vista del negocio pasando el objeto/ID en 'business'
        $dashboardUrl = route('businesses.profile', ['business' => $business->id]);

        $params = [
            'restaurant_name' => $business->name,
            'status_label'    => $statusLabel,
            'dashboard_url'   => $dashboardUrl,
        ];

        $allSent = true;

        foreach ($users as $user) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $user->phone);

            if (empty($cleanPhone)) {
                continue;
            }

            try {
                Log::info("Notificando estado ({$action}) a {$user->name} para el restaurante {$business->name}");
                
                $sent = WhatsApp::sendTemplate($cleanPhone, 'business_status_change', $params);
                
                if (!$sent) {
                    $allSent = false;
                }
            } catch (\Exception $e) {
                $allSent = false;
                Log::error("Error al enviar notificación de cambio de estado a {$user->name}: " . $e->getMessage());
            }
        }

        return $allSent;
    }
}