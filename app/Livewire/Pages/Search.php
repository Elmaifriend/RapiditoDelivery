<?php

namespace App\Livewire\Pages;

use App\Models\Business;
use App\Models\DeliveryAddress;
use App\Models\ServiceZone;
use App\Models\Tag;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Search Product')]
class Search extends Component
{
    public string $search = '';

    public $city = null;

    public function mount()
    {
        $guestToken = request()->cookie('guest_token');

        if ($guestToken) {
            $address = DeliveryAddress::where('guest_token', $guestToken)->latest()->first();

            if ($address && $address->lat && $address->lng) {
                $this->resolveServiceZone($address->lat, $address->lng);
            }
        }
    }

    protected function resolveServiceZone($lat, $lng)
    {
        $zone = ServiceZone::active()
            ->with('city.businesses')
            ->get()
            ->first(fn ($zone) => $zone->contains($lat, $lng));

        if ($zone) {
            $this->city = $zone->city;
        }
    }

    #[Computed]
    public function businesses()
    {
        if (strlen($this->search) < 2) {
            return collect();
        }

        return Business::active()
            ->where('name', 'like', '%'.$this->search.'%')
            ->when($this->city, function ($query) {
                $query->where('city_id', $this->city->id);
            })
            ->get();
    }

    #[Computed]
    public function topTags()
    {
        return Tag::whereIn('name', ['Hamburguesas', 'Vegano', 'Café', 'Postres'])->get();
    }

    public function render()
    {
        return view('livewire.pages.search');
    }
}
