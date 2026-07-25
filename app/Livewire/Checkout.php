<?php

namespace App\Livewire;

use App\Enums\CountryCode;
use App\Enums\PaymentMethod;
use App\Models\Cart;
use App\Models\DeliveryAddress;
use App\Services\ConvertCartToOrderService;
use App\Services\DeliveryFeeCalculatorService;
use App\Services\WhatsAppNotifierService;
use Illuminate\Support\Facades\Cookie;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Checkout')]
class Checkout extends Component
{
    // Datos del cliente
    public string $customerName = '';

    public string $countryCode = 'MX'; // Lada por defecto

    public string $customerPhone = '';

    // Datos del formulario
    public string $specialInstructions = ''; // Para la cocina (ej: sin cebolla)

    public string $deliveryInstructions = ''; // Para el repartidor (ej: tocar timbre B)

    public string $reference = '';

    public string $paymentMethod = 'cash';

    // Datos de tarjeta
    public string $cardNumber = '';

    public string $cardExpiry = '';

    public string $cardCvv = '';

    // Dirección única activa para el Checkout
    public ?int $selectedAddressId = null;

    public function mount()
    {
        if (auth()->check()) {
            $user = auth()->user();
            $this->customerName = $user->name ?? '';

            // Si el usuario tiene teléfono guardado, intentamos extraer la lada si existe
            if (! empty($user->phone)) {
                $this->parsePhoneNumber($user->phone);
            }
        }

        $defaultAddress = $this->addresses->where('is_default', true)->first()
            ?? $this->addresses->first();

        if ($defaultAddress) {
            $this->selectedAddressId = $defaultAddress->id;
            $this->reference = $defaultAddress->reference ?? '';
        }
    }

    private function parsePhoneNumber(string $phone)
    {
        // Intenta separar el prefijo si ya viene guardado con lada (ej: +526641234567)
        foreach (CountryCode::cases() as $code) {
            $dial = $code->dialCode();
            if (str_starts_with($phone, $dial)) {
                $this->countryCode = $code->name;
                $this->customerPhone = substr($phone, strlen($dial));

                return;
            }
        }
        $this->customerPhone = $phone;
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
        $userId = auth()->id();
        $guestToken = Cookie::get('guest_token');

        return DeliveryAddress::where(fn ($query) => $userId
            ? $query->where('user_id', $userId)
            : $query->where('guest_token', $guestToken)
        )->get();
    }

    #[Computed]
    public function currentAddress()
    {
        return $this->addresses->firstWhere('id', $this->selectedAddressId);
    }

    #[Computed]
    public function deliveryFee()
    {
        if (! $this->cart || ! $this->cart->business || ! $this->currentAddress) {
            return null;
        }

        $business = $this->cart->business;
        $address = $this->currentAddress;

        if (! $business->lat || ! $business->lng || ! $address->lat || ! $address->lng) {
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
        if (! $this->cart) {
            return 0;
        }

        $fee = $this->deliveryFee ?? 0;

        return $this->cart->subtotal + $fee;
    }

    public function confirmPayment(ConvertCartToOrderService $orderService)
    {
        $this->validate([
            'customerName' => 'required|string|max:100',
            'countryCode' => 'required|in:'.implode(',', array_column(CountryCode::cases(), 'name')),
            'customerPhone' => 'required|string|min:7|max:15',
            'reference' => 'required|string|max:255',
            'deliveryInstructions' => 'nullable|string|max:255',
            'specialInstructions' => 'nullable|string|max:255',
            'paymentMethod' => 'required|in:card,cash',
            'cardNumber' => 'required_if:paymentMethod,card',
            'cardExpiry' => 'required_if:paymentMethod,card',
            'cardCvv' => 'required_if:paymentMethod,card',
        ], [
            'customerName.required' => 'Por favor escribe tu nombre completo para la entrega.',
            'customerPhone.required' => 'El número de teléfono es obligatorio para contactarte.',
            'customerPhone.min' => 'El teléfono ingresado es muy corto.',
            'reference.required' => 'Es importante escribir una referencia para ayudar al repartidor.',
            'cardNumber.required_if' => 'El número de tarjeta es obligatorio.',
            'cardExpiry.required_if' => 'La fecha de expiración es obligatoria.',
            'cardCvv.required_if' => 'El código de seguridad CVV es obligatorio.',
        ]);

        $cart = $this->cart;
        $address = $this->currentAddress;

        if (! $cart) {
            $this->addError('general', 'El carrito no es válido.');

            return;
        }

        if (! $address) {
            $this->addError('address', 'Por favor selecciona una dirección de entrega válida.');

            return;
        }

        if (is_null($this->deliveryFee)) {
            $this->addError('address', 'La dirección seleccionada está fuera de nuestra zona de cobertura.');

            return;
        }

        // Armamos el número completo con su lada internacional
        $selectedEnum = CountryCode::fromName($this->countryCode) ?? CountryCode::MX;
        $fullPhoneNumber = $selectedEnum->dialCode().preg_replace('/\D/', '', $this->customerPhone);

        // 1. Guardamos la nueva referencia directamente en el modelo de la dirección
        $address->update([
            'reference' => $this->reference,
            'last_used_at' => now(),
        ]);

        // 2. Actualizamos los totales del carrito actual
        $cart->update([
            'delivery_fee' => $this->deliveryFee,
            'total' => $this->totalAmount,
        ]);

        // 3. Convertimos el carrito en una Orden (specialInstructions pasa al restaurante/cocina)
        $order = $orderService->execute(
            $cart,
            $address,
            PaymentMethod::from($this->paymentMethod),
            $this->specialInstructions
        );

        if ($order) {
            $order->update([
                'customer_name' => $this->customerName,
                'customer_phone' => $fullPhoneNumber,
            ]);

            // Guardamos las referencias e instrucciones de entrega en la tabla 'order_dropoff_locations'
            if (method_exists($order, 'dropoffLocations')) {
                $order->dropoffLocations()->update([
                    'reference' => $this->reference,
                    'delivery_instructions' => $this->deliveryInstructions,
                ]);
            }

            // 4. Mandar notificaciones de WhatsApp
            $notifier = new WhatsAppNotifierService;
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
