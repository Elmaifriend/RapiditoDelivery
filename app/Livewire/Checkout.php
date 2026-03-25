<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Cart;
use App\Models\DeliveryAddress;
use App\Services\ConvertCartToOrderService;
use Illuminate\Support\Facades\Cookie;

#[Title('Checkout')] 
class Checkout extends Component 
{
    // Datos del formulario
    public string $specialInstructions = '';
    public string $paymentMethod = 'card'; // 'card' o 'cash'
    
    // Si quisieras capturar datos de tarjeta (OJO: en producción usa tokens de Stripe/Conekta, no guardes esto directo)
    public string $cardNumber = '';
    public string $cardExpiry = '';
    public string $cardCvv = '';

    // Dirección seleccionada
    public ?int $selectedAddressId = null;

    public function mount()
    {
        // Al cargar la página, pre-seleccionamos la dirección default si existe
        $address = $this->getDefaultAddress();
        if ($address) {
            $this->selectedAddressId = $address->id;
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

    public function confirmPayment(ConvertCartToOrderService $orderService)
    {
        $cart = $this->getCurrentCart();
        $address = DeliveryAddress::find($this->selectedAddressId);

        if (!$cart) {
            $this->addError('general', 'El carrito no es válido.');
            return;
        }

        if (!$address) {
            $this->addError('address', 'Por favor selecciona una dirección de entrega.');
            return;
        }

        // Aquí llamamos al servicio que creamos antes
        $order = $orderService->execute(
            $cart, 
            $address, 
            $this->paymentMethod, 
            $this->specialInstructions
        );

        // Redirigimos
        //return redirect()->route('orders.show', $order->id);
    }

    private function getCurrentCart(): ?Cart
    {
        $token = Cookie::get('guest_token');
        return Cart::with('items')->where('guest_token', $token)->first();
    }

    private function getDefaultAddress(): ?DeliveryAddress
    {
        $token = Cookie::get('guest_token');
        return DeliveryAddress::where('guest_token', $token)->latest('last_used_at')->first();
    }

    public function render()
    {
        return view('livewire.checkout', [
            'cart' => $this->getCurrentCart()
        ]);
    }
}