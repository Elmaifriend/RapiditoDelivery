<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Order;
use App\WhatsApp\WhatsApp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Models\Driver;

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
    
    /**
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

        // Generamos dinámicamente la URL firmada con expiración de 12 horas
        $dashboardUrl = URL::temporarySignedRoute(
            'kitchen.orders',
            now()->addHours(12),
            ['businessId' => $business->id]
        );

        // Parámetros de la plantilla de Meta
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

        // Generamos la URL firmada con expiración de 12 horas
        $shiftUrl = URL::temporarySignedRoute(
            'driver.profile',
            now()->addHours(12),
            ['driver' => $driver->id]
        );

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

        // Generamos la URL firmada con expiración de 12 horas
        $dashboardUrl = URL::temporarySignedRoute(
            'businesses.profile',
            now()->addHours(12),
            ['business' => $business->id]
        );

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

    /**
     * Envía un mensaje al cliente informándole que el restaurante aceptó su orden.
     */
    public function notifyCustomerOrderAccepted(Order $order): bool
    {
        $phone = $order->customer_phone;

        if (!$phone) {
            Log::warning("No se pudo notificar al cliente sobre la orden {$order->id}: no hay número de teléfono asignado.");
            return false;
        }

        if (!$order->relationLoaded('business')) {
            $order->load('business');
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($cleanPhone)) {
            Log::error("Teléfono de cliente inválido en orden {$order->id}: '{$phone}'");
            return false;
        }

        $params = [
            'name'            => $order->customer_name ?? 'Cliente',
            'restaurant_name' => $order->business?->name ?? 'el restaurante',
            'order_id'        => $order->id,
        ];

        try {
            Log::info("Notificando a cliente ({$cleanPhone}) aceptación de la orden #{$order->id}");

            return WhatsApp::sendTemplate($cleanPhone, 'order_accepted_customer', $params);
        } catch (\Exception $e) {
            Log::error("Error al notificar al cliente aceptación de la orden {$order->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envía un mensaje al cliente informándole que el restaurante rechazó su orden.
     */
    public function notifyCustomerOrderRejected(Order $order): bool
    {
        $phone = $order->customer_phone;

        if (!$phone) {
            Log::warning("No se pudo notificar al cliente sobre el rechazo de la orden {$order->id}: no hay número de teléfono asignado.");
            return false;
        }

        if (!$order->relationLoaded('business')) {
            $order->load('business');
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($cleanPhone)) {
            Log::error("Teléfono de cliente inválido en orden {$order->id}: '{$phone}'");
            return false;
        }

        $params = [
            'name'            => $order->customer_name ?? 'Cliente',
            'restaurant_name' => $order->business?->name ?? 'el restaurante',
            'order_id'        => $order->id,
        ];

        try {
            Log::info("Notificando a cliente ({$cleanPhone}) rechazo de la orden #{$order->id}");

            return WhatsApp::sendTemplate($cleanPhone, 'order_rejected_customer', $params);
        } catch (\Exception $e) {
            Log::error("Error al notificar al cliente rechazo de la orden {$order->id}: " . $e->getMessage());
            return false;
        }
    }

    public function notifyDriverNewOrderAssignment(Order $order, Driver $driver): bool
    {
        // Cargamos la relación user si no se ha cargado previamente
        if (!$driver->relationLoaded('user')) {
            $driver->load('user');
        }

        $phone = $driver->user?->phone;

        if (!$phone) {
            Log::warning("No se pudo notificar al repartidor {$driver->id} sobre la orden {$order->id}: el usuario asociado no tiene número asignado.");
            return false;
        }

        if (!$order->relationLoaded('business')) {
            $order->load('business');
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($cleanPhone)) {
            Log::error("Teléfono de repartidor inválido para el conductor {$driver->id}: '{$phone}'");
            return false;
        }

        $params = [
            'driver_name'     => $driver->user?->name ?? 'Repartidor',
            'order_id'        => $order->id,
            'restaurant_name' => $order->business?->name ?? 'el restaurante',
        ];

        try {
            Log::info("Notificando a repartidor {$driver->id} ({$cleanPhone}) asignación de orden #{$order->id}");

            return WhatsApp::sendTemplate($cleanPhone, 'driver_new_order', $params);
        } catch (\Exception $e) {
            Log::error("Error al notificar al repartidor {$driver->id} para la orden {$order->id}: " . $e->getMessage());
            return false;
        }
    }
}