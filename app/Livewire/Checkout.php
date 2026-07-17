<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Cart;
use App\Models\DeliveryAddress;
use App\Services\DeliveryFeeCalculatorService;
use App\Services\ConvertCartToOrderService;
use App\Services\WhatsAppNotifierService; 
use Illuminate\Support\Facades\Cookie;
use App\Enums\PaymentMethod; // <-- Enum importado correctamente

#[Title('Checkout')] 
class Checkout extends Component 
{
    // Datos del cliente (NUEVO)
    public string $customerName = '';
    public string $customerPhone = '';

    // Datos del formulario
    public string $specialInstructions = '';
    public string $reference = ''; 
    public string $paymentMethod = 'card';
    
    // Datos de tarjeta
    public string $cardNumber = '';
    public string $cardExpiry = '';
    public string $cardCvv = '';

    // Dirección única activa para el Checkout
    public ?int $selectedAddressId = null;

    public function mount()
    {
        // NUEVO: Intentar precargar datos si el usuario ya está autenticado
        if (auth()->check()) {
            $user = auth()->user();
            $this->customerName = $user->name ?? '';
            $this->customerPhone = $user->phone ?? ''; // Ajusta 'phone' si en tu BD de usuarios se llama diferente
        }

        // Traemos la dirección que se acaba de guardar o confirmar obligatoriamente en el paso previo
        $defaultAddress = $this->addresses->where('is_default', true)->first() 
            ?? $this->addresses->first();

        if ($defaultAddress) {
            $this->selectedAddressId = $defaultAddress->id;
            // Precargamos la referencia existente si el usuario ya tenía una guardada
            $this->reference = $defaultAddress->reference ?? '';
        }
    }

    public function setPaymentMethod(string $method)
    {
        $this->paymentMethod = $method;
    }

    #[Computed]
    public function cart()
    {
        $userId = auth()->id();
        $guestToken = Cookie::get('guest_token');

        return Cart::with(['business', 'items'])
            ->where('status', 'active')
            ->where(fn ($query) => $userId 
                ? $query->where('user_id', $userId) 
                : $query->where('guest_token', $guestToken)
            )->first();
    }

    #[Computed]
    public function addresses()
    {
        $guestToken = Cookie::get('guest_token');
        return DeliveryAddress::where('guest_token', $guestToken)->get();
    }

    #[Computed]
    public function currentAddress()
    {
        return $this->addresses->firstWhere('id', $this->selectedAddressId);
    }

    #[Computed]
    public function deliveryFee()
    {
        if (!$this->cart || !$this->cart->business || !$this->currentAddress) {
            return null;
        }

        $business = $this->cart->business;
        $address = $this->currentAddress;

        if (!$business->lat || !$business->lng || !$address->lat || !$address->lng) {
            return null; 
        }

        return app(DeliveryFeeCalculatorService::class)->calculate(
            $business->lat,
            $business->lng,
            $address->lat,
            $address->lng
        );
    }

    #[Computed]
    public function totalAmount()
    {
        if (!$this->cart) return 0;
        
        $fee = $this->deliveryFee ?? 0;
        return $this->cart->subtotal + $fee;
    }

    public function confirmPayment(ConvertCartToOrderService $orderService)
    {
        // Validamos incluyendo los nuevos campos (NUEVO)
        $this->validate([
            'customerName' => 'required|string|max:100',
            'customerPhone' => 'required|string|min:8|max:20', // Validación flexible de teléfono
            'reference' => 'required|string|max:255',
            'specialInstructions' => 'nullable|string|max:255',
            'paymentMethod' => 'required|in:card,cash',
            'cardNumber' => 'required_if:paymentMethod,card',
            'cardExpiry' => 'required_if:paymentMethod,card',
            'cardCvv' => 'required_if:paymentMethod,card',
        ], [
            'customerName.required' => 'Por favor escribe tu nombre completo para la entrega.',
            'customerPhone.required' => 'El número de teléfono es obligatorio para contactarte.',
            'reference.required' => 'Es importante escribir una referencia para ayudar al repartidor.',
            'cardNumber.required_if' => 'El número de tarjeta es obligatorio.',
            'cardExpiry.required_if' => 'La fecha de expiración es obligatoria.',
            'cardCvv.required_if' => 'El código de seguridad CVV es obligatorio.'
        ]);

        $cart = $this->cart;
        $address = $this->currentAddress;

        if (!$cart) {
            $this->addError('general', 'El carrito no es válido.');
            return;
        }

        if (!$address) {
            $this->addError('address', 'Por favor selecciona una dirección de entrega válida.');
            return;
        }

        if (is_null($this->deliveryFee)) {
            $this->addError('address', 'La dirección seleccionada está fuera de nuestra zona de cobertura.');
            return;
        }

        // 1. Guardamos la nueva referencia directamente en el modelo de la dirección
        $address->update([
            'reference' => $this->reference,
            'last_used_at' => now()
        ]);

        // 2. Actualizamos los totales del carrito actual
        $cart->update([
            'delivery_fee' => $this->deliveryFee,
            'total' => $this->totalAmount
        ]);

        // 3. Convertimos el carrito en una Orden casteando la cadena a un Enum de PaymentMethod (CORREGIDO)
        $order = $orderService->execute(
            $cart, 
            $address, 
            PaymentMethod::from($this->paymentMethod), 
            $this->specialInstructions
        );

        if ($order) {
            // NUEVO: Asignamos directamente los campos a la orden recién creada para que se guarden
            $order->update([
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
            ]);

            // Si tu Service no inyecta automáticamente la referencia al OrderDropoffLocation:
            if (method_exists($order, 'dropoffLocations')) {
                $order->dropoffLocations()->update(['reference' => $this->reference]);
            }

            // 4. Mandar notificaciones de WhatsApp
            $notifier = new WhatsAppNotifierService();
            $notifier->notifyCustomerOrderCreated($order);
            $notifier->notifyRestaurantNewOrder($order);

            return redirect()->route('checkout.success', $order->id);
        }
    }

    public function render()
    {
        return view('livewire.checkout');
    }
}