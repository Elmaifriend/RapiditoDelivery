<?php

namespace App\Livewire\Pages;

use App\Models\Business;
use App\Models\Cart;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Business')]
class BusinessPage extends Component
{
    public Business $business;

    public ?Product $selectedProduct = null;

    public int $quantity = 1;

    public bool $showProductModal = false;

    public bool $showStartNewCartModal = false;

    public function mount(Business $business)
    {
        $this->business = $business->load(['category', 'productCategories.products']);
        $guestToken = request()->cookie('guest_token');

        Cart::where('status', 'active')
            ->where('business_id', $this->business->id)
            ->where('guest_token', '=', $guestToken)
            ->first();
    }

    #[Computed]
    public function cart()
    {
        $userId = auth()->id();
        $guestToken = request()->cookie('guest_token');

        return Cart::where('status', 'active')
            ->where('business_id', $this->business->id)
            ->where(function ($query) use ($userId, $guestToken) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('guest_token', $guestToken);
                }
            })
            ->first();
    }

    public function openProductModal(Product $product)
    {
        $guestToken = request()->cookie('guest_token');
        $otherCart = Cart::where('status', 'active')
            ->where('business_id', '!=', $this->business->id)
            ->where('guest_token', '=', $guestToken)
            ->exists();

        if ($otherCart) {
            $this->selectedProduct = $product;
            $this->showStartNewCartModal = true;

            return;
        }

        $this->selectedProduct = $product;

        $itemInCart = $this->cart?->items->where('product_id', $product->id)->first();
        $this->quantity = $itemInCart?->quantity ?? 1;

        $this->showProductModal = true;
    }

    public function increment()
    {
        $this->quantity++;
    }

    public function closeModal()
    {
        $this->showProductModal = false;
        $this->showStartNewCartModal = false;
    }

    public function decrement()
    {
        if ($this->quantity > 0) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        if (! $this->selectedProduct) {
            return;
        }

        $userId = auth()->id();
        $guestToken = request()->cookie('guest_token');

        $cart = Cart::firstOrCreate(
            [
                'user_id' => $userId,
                'guest_token' => $guestToken,
                'status' => 'active',
                'business_id' => $this->business->id,
            ],
            ['expires_at' => now()->addDays(3)],
        );

        $cartItem = $cart->items()->where('product_id', $this->selectedProduct->id)->first();

        if ($this->quantity <= 0) {
            $cartItem?->delete();
        } else {
            if ($cartItem) {
                $cartItem->update([
                    'quantity' => $this->quantity,
                    'subtotal' => $this->selectedProduct->price * $this->quantity,
                ]);
            } else {
                $cart->items()->create([
                    'product_id' => $this->selectedProduct->id,
                    'product_name_snapshot' => $this->selectedProduct->name,
                    'product_description_snapshot' => $this->selectedProduct->description,
                    'product_image_url_snapshot' => $this->selectedProduct->image_path,
                    'price_snapshot' => $this->selectedProduct->price,
                    'quantity' => $this->quantity,
                    'subtotal' => $this->selectedProduct->price * $this->quantity,
                ]);
            }
        }

        $cart->recalculateTotals();
        $this->closeModal();
        $this->dispatch('cart-updated');
    }

    public function clearCart()
    {
        $userId = auth()->id();
        $guestToken = request()->cookie('guest_token');

        Cart::where('status', 'active')
            ->where('business_id', '!=', $this->business->id)
            ->where(function ($query) use ($userId, $guestToken) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('guest_token', $guestToken);
                }
            })
            ->delete();

        $this->showStartNewCartModal = false;
        $this->quantity = 1;
        $this->showProductModal = true;
    }

    public function render()
    {
        return view('livewire.pages.business');
    }
}
