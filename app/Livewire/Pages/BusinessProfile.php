<?php

namespace App\Livewire\Pages;

use App\Models\Business;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Locked;

class BusinessProfile extends Component
{
    #[Locked]
    public int $businessId;

    public string $activeTab = 'staff'; // 'products' o 'staff'

    public function mount(Business $business)
    {
        $this->businessId = $business->id;
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * Alterna la apertura/cierre general del restaurante.
     */
    public function toggleOpenStatus()
    {
        $business = Business::findOrFail($this->businessId);

        $business->update([
            'is_open' => !$business->is_open,
        ]);

        $this->dispatch('business-status-updated');
    }

    /**
     * Alterna la disponibilidad de un producto específico (Agotado / Disponible).
     */
    public function toggleProductAvailability(int $productId)
    {
        $product = Product::where('business_id', $this->businessId)
            ->findOrFail($productId);

        $product->update([
            'is_available' => !$product->is_available,
        ]);

        $this->dispatch('product-status-updated');
    }

    public function render()
    {
        $business = Business::with(['category', 'city', 'users', 'products' => function ($query) {
            $query->active()->orderBy('sort_order', 'asc');
        }])->findOrFail($this->businessId);

        return view('livewire.pages.business-profile', [
            'business' => $business,
        ])->layout('layouts.app');
    }
}