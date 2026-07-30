<?php

namespace App\Livewire\Pages;

use App\Models\DeliveryAddress;
use App\Models\ServiceZone;
use App\Models\Tag;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tag')]
class TagPage extends Component
{
    public Tag $tag;

    public $city = null;

    public function mount(Tag $tag)
    {
        $this->tag = $tag;
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
        return $this->tag->businesses()
            ->active()
            ->when($this->city, function ($query) {
                $query->where('city_id', $this->city->id);
            })
            ->get();
    }

    public function render()
    {
        return view('livewire.pages.tag');
    }
}
