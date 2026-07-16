<?php

use App\Enums\DeliveryStatus;
use App\Enums\OrderLifecycleStatus;
use App\Enums\PaymentStatus;
use App\Enums\RestaurantDecisionStatus;
use App\Models\Business;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CreateOrderFromCartService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it successfully converts a cart and temporary address into an order', function () {
    // Arrange
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $product1 = Product::factory()->create(['price' => 10.00]);
    $product2 = Product::factory()->create(['price' => 15.00]);

    $guestToken = 'test-guest-token-123';

    // Create a temporary address matching the guest token
    $temporaryAddress = DeliveryAddress::factory()->create([
        'user_id' => $user->id,
        'guest_token' => $guestToken,
        'formatted_address' => '123 Test St',
        'address_line' => 'Apt 4B',
        'reference' => 'Near the park',
        'source' => 'WEB',
        'city' => 'Test City',
        'state' => 'Test State',
        'country' => 'Test Country',
        'lat' => -12.345678,
        'lng' => -76.543210,
        'place_id' => 'place-abc-123',
    ]);

    // Create cart and items
    $cart = Cart::create([
        'user_id' => $user->id,
        'guest_token' => $guestToken,
        'business_id' => $business->id,
        'subtotal' => 35.00,
        'delivery_fee' => 5.00,
        'total' => 40.00,
        'status' => 'active',
    ]);

    $cartItem1 = CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product1->id,
        'product_name_snapshot' => $product1->name,
        'product_description_snapshot' => $product1->description,
        'product_image_url_snapshot' => null,
        'price_snapshot' => 10.00,
        'quantity' => 2,
        'subtotal' => 20.00,
    ]);

    $cartItem2 = CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product2->id,
        'product_name_snapshot' => $product2->name,
        'product_description_snapshot' => $product2->description,
        'product_image_url_snapshot' => null,
        'price_snapshot' => 15.00,
        'quantity' => 1,
        'subtotal' => 15.00,
    ]);

    $customerData = [
        'customer_name' => 'John Doe',
        'customer_phone' => '+51999999999',
        'payment_method' => 'cash',
        'special_instructions' => 'Call when outside',
    ];

    $service = new CreateOrderFromCartService;

    // Act
    $order = $service->execute($cart, $customerData);

    // Assert
    expect($order)->toBeInstanceOf(Order::class);

    // Check main order details
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'user_id' => $user->id,
        'business_id' => $business->id,
        'guest_token' => $guestToken,
        'customer_name' => 'John Doe',
        'customer_phone' => '+51999999999',
        'lifecycle_status' => OrderLifecycleStatus::PENDING->value,
        'business_decision_status' => RestaurantDecisionStatus::PENDING->value,
        'delivery_status' => DeliveryStatus::PENDING->value,
        'payment_status' => PaymentStatus::PENDING->value,
        'special_instructions' => 'Call when outside',
        'payment_method' => 'cash',
        'subtotal' => 35.00,
        'delivery_fee' => 5.00,
        'total' => 40.00,
    ]);

    // Check order dropoff location was duplicated correctly
    $this->assertDatabaseHas('order_dropoff_locations', [
        'order_id' => $order->id,
        'formatted_address' => '123 Test St',
        'address_line' => 'Apt 4B',
        'reference' => 'Near the park',
        'source' => 'WEB',
        'city' => 'Test City',
        'state' => 'Test State',
        'country' => 'Test Country',
        'lat' => -12.345678,
        'lng' => -76.543210,
        'place_id' => 'place-abc-123',
    ]);

    // Check order items were migrated correctly
    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $product1->id,
        'product_name_snapshot' => $product1->name,
        'price_snapshot' => 10.00,
        'quantity' => 2,
        'subtotal' => 20.00,
    ]);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $product2->id,
        'product_name_snapshot' => $product2->name,
        'price_snapshot' => 15.00,
        'quantity' => 1,
        'subtotal' => 15.00,
    ]);

    // Check cart status was updated to converted
    expect($cart->fresh()->status)->toBe('converted');

    // Check temporary address was deleted
    $this->assertDatabaseMissing('delivery_addresses', [
        'id' => $temporaryAddress->id,
    ]);
});

test('it throws an exception when no temporary address exists for the cart guest_token', function () {
    // Arrange
    $user = User::factory()->create();
    $business = Business::factory()->create();

    $cart = Cart::create([
        'user_id' => $user->id,
        'guest_token' => 'non-existent-token',
        'business_id' => $business->id,
        'subtotal' => 10.00,
        'delivery_fee' => 2.00,
        'total' => 12.00,
        'status' => 'active',
    ]);

    $customerData = [
        'customer_name' => 'John Doe',
        'customer_phone' => '+51999999999',
        'payment_method' => 'cash',
    ];

    $service = new CreateOrderFromCartService;

    // Act & Assert
    expect(fn () => $service->execute($cart, $customerData))
        ->toThrow(Exception::class, 'No se encontró una ubicación seleccionada para este pedido.');
});
