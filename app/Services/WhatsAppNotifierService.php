<?php

namespace App\Services;

use App\Models\Order;
use App\WhatsApp\WhatsApp;
use Illuminate\Support\Facades\Log;

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
}