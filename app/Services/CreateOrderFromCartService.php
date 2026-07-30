<?php

namespace App\Services;

use App\Enums\BusinessDecisionStatus;
use App\Enums\DeliveryStatus;
use App\Enums\OrderLifecycleStatus;
use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\DeliveryAddress;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateOrderFromCartService
{
    /**
     * Convierte un carrito y su dirección temporal en una orden definitiva.
     *
     * @param  Cart  $cart  El carrito activo
     * @param  array  $customerData  Datos del formulario de checkout (name, phone, payment_method, etc.)
     *
     * @throws Exception
     */
    public function execute(Cart $cart, array $customerData): Order
    {
        // 1. Validar que exista la ubicación del mapa asociada a este guest_token
        $temporaryAddress = DeliveryAddress::where('guest_token', $cart->guest_token)
            ->latest('updated_at')
            ->first();

        if (! $temporaryAddress) {
            throw new Exception('No se encontró una ubicación seleccionada para este pedido.');
        }

        return DB::transaction(function () use ($cart, $customerData, $temporaryAddress) {

            // 2. Registrar la Orden principal
            $order = Order::create([
                'user_id' => $cart->user_id,
                'business_id' => $cart->business_id,
                'guest_token' => $cart->guest_token,
                'customer_name' => $customerData['customer_name'],
                'customer_phone' => $customerData['customer_phone'],
                'lifecycle_status' => OrderLifecycleStatus::PENDING,
                'business_decision_status' => BusinessDecisionStatus::PENDING,
                'delivery_status' => DeliveryStatus::PENDING,
                'payment_status' => PaymentStatus::PENDING,
                'special_instructions' => $customerData['special_instructions'] ?? null,
                'payment_method' => $customerData['payment_method'],
                'subtotal' => $cart->subtotal,
                'delivery_fee' => $cart->delivery_fee,
                'total' => $cart->total,
            ]);

            // 3. Duplicar la ubicación seleccionada en el Dropoff definitivo de la orden
            $order->dropoffLocations()->create([
                'formatted_address' => $temporaryAddress->formatted_address,
                'address_line' => $temporaryAddress->address_line,
                'reference' => $temporaryAddress->reference,
                'source' => $temporaryAddress->source,
                'city' => $temporaryAddress->city,
                'state' => $temporaryAddress->state,
                'country' => $temporaryAddress->country,
                'lat' => $temporaryAddress->lat,
                'lng' => $temporaryAddress->lng,
                'place_id' => $temporaryAddress->place_id,
            ]);

            // 4. Mudar los ítems convirtiéndolos en Snapshots fijos
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

            // 5. Cambiar estado del carrito
            $cart->update([
                'status' => 'converted',
            ]);

            // 6. Eliminar la dirección temporal del mapa para no dejar basura de invitados
            $temporaryAddress->delete();

            return $order;
        });
    }
}
