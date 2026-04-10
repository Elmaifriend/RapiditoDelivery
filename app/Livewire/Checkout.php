<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Cart;
use App\Models\DeliveryAddress;
use App\Services\DeliveryFeeCalculatorService;
use App\Services\ConvertCartToOrderService;
use Illuminate\Support\Facades\Cookie;

#[Title('Checkout')] 
class Checkout extends Component 
{
    // Datos del formulario
    public string $specialInstructions = '';
    public string $paymentMethod = 'card';
    
    // Datos de tarjeta
    public string $cardNumber = '';
    public string $cardExpiry = '';
    public string $cardCvv = '';

    // Dirección seleccionada
    public ?int $selectedAddressId = null;

    public function mount()
    {
        // Pre-seleccionamos la dirección por defecto o la primera que encontremos
        $defaultAddress = $this->addresses->where('is_default', true)->first() 
            ?? $this->addresses->first();

        if ($defaultAddress) {
            $this->selectedAddressId = $defaultAddress->id;
        }
    }

    public function setPaymentMethod(string $method)
    {
        $this->paymentMethod = $method;
    }

    public function selectAddress(int $addressId)
    {
        $this->selectedAddressId = $addressId;
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
    public function deliveryFee()
    {
        if (!$this->cart || !$this->cart->business || !$this->selectedAddressId) {
            return null;
        }

        $business = $this->cart->business;
        $address = $this->addresses->firstWhere('id', $this->selectedAddressId);

        if (!$business->lat || !$business->lng || !$address || !$address->lat || !$address->lng) {
            return null; // Faltan coordenadas para calcular
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
        $cart = $this->cart;
        $address = $this->addresses->firstWhere('id', $this->selectedAddressId);

        if (!$cart) {
            $this->addError('general', 'El carrito no es válido.');
            return;
        }

        if (!$address) {
            $this->addError('address', 'Por favor selecciona una dirección de entrega.');
            return;
        }

        // Si el servicio retornó null, significa que la dirección está fuera de los polígonos
        if (is_null($this->deliveryFee)) {
            $this->addError('address', 'La dirección seleccionada está fuera de nuestra zona de cobertura.');
            return;
        }

        // Actualizamos el costo exacto antes de mandar la orden a crear
        $cart->update([
            'delivery_fee' => $this->deliveryFee,
            'total' => $this->totalAmount
        ]);

        $order = $orderService->execute(
            $cart, 
            $address, 
            $this->paymentMethod, 
            $this->specialInstructions
        );

        // return redirect()->route('orders.show', $order->id);
    }

    public function render()
    {
        return view('livewire.checkout');
    }
}