<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\DeliveryAddress;
use App\Enums\OrderLifecycleStatus;
use App\Enums\BusinessDecisionStatus;
use App\Enums\DeliveryStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use App\Enums\AddressSource;
use Illuminate\Support\Facades\DB;

class ConvertCartToOrderService
{
    /**
     * Convierte un carrito activo en una nueva orden con sus estados iniciales correspondientes.
     */
    public function execute(
        Cart $cart, 
        DeliveryAddress $address, 
        PaymentMethod $paymentMethod, 
        ?string $specialInstructions = null
    ): Order {
        // Carga la relación 'business' si no viene pre-cargada para evitar queries extras durante la transacción
        $cart->loadMissing('business');

        return DB::transaction(function () use ($cart, $address, $paymentMethod, $specialInstructions) {
            
            // 1. Crear la Orden principal con la ciudad asignada directamente desde el negocio
            $order = Order::create([
                'user_id' => $cart->user_id, // Soporta null para usuarios invitados (guests)
                'city_id' => $cart->business->city_id,
                'business_id' => $cart->business_id,
                
                // El cliente finalizó el checkout; la orden es real y válida en el sistema
                'lifecycle_status' => OrderLifecycleStatus::CONFIRMED, 
                
                // Flujos paralelos operativos en estado inicial pendiente
                'business_decision_status' => BusinessDecisionStatus::PENDING,
                'delivery_status' => DeliveryStatus::PENDING,
                'payment_status' => PaymentStatus::PENDING,
                
                'special_instructions' => $specialInstructions,
                'subtotal' => $cart->subtotal,
                'delivery_fee' => $cart->delivery_fee,
                'total' => $cart->total,
                'payment_method' => $paymentMethod,
            ]);

            // 2. Transferir los Items del carrito a la orden
            foreach ($cart->items as $cartItem) {
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'product_name_snapshot' => $cartItem->product_name_snapshot,
                    'product_description_snapshot' => $cartItem->product_description_snapshot,
                    'product_image_url_snapshot' => $cartItem->product_image_url_snapshot,
                    'price_snapshot' => $cartItem->price_snapshot,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $cartItem->subtotal,
                ]);
            }

            // 3. Crear la ubicación de entrega (Dropoff Location)
            $order->dropoffLocations()->create([
                'formatted_address' => $address->formatted_address,
                'lat' => $address->lat,
                'lng' => $address->lng,
                'source' => $address->source ?? AddressSource::WEB,
            ]);

            // 4. Marcar el carrito como convertido y completado
            $cart->update([
                'status' => 'completed', 
                'converted_to_order' => true,
                'converted_to_order_at' => now(),
            ]);

            return $order;
        });
    }
}