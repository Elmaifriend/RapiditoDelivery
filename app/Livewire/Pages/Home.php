<?php

namespace App\Livewire\Pages;

use App\Enums\AddressSource;
use App\Models\DeliveryAddress;
use App\Models\ServiceZone;
use App\Models\Tag;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Home')]
class Home extends Component
{
    public ?float $lat = null;

    public ?float $lng = null;

    public $city = null;

    public bool $noService = false;

    public bool $locationDenied = false;

    public bool $hasAddress = false;

    protected $listeners = [
        'locationDetected' => 'setLocation',
        'locationDenied' => 'handleDenied',
    ];

    public function mount()
    {
        $guestToken = request()->cookie('guest_token');

        if ($guestToken) {
            $address = DeliveryAddress::where('guest_token', $guestToken)->latest()->first();

            if ($address && $address->lat && $address->lng) {
                $this->lat = $address->lat;
                $this->lng = $address->lng;
                $this->hasAddress = true;
                $this->resolveServiceZone();
            }
        }
    }

    public function handleDenied()
    {
        $this->locationDenied = true;
        $this->noService = false;
        $this->city = null;
        $this->dispatch('addressUpdated');
    }

    public function setLocation($lat, $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->saveAddress();
        $this->resolveServiceZone();
        $this->hasAddress = true;
        $this->dispatch('addressUpdated');
    }

    protected function resolveServiceZone()
    {
        $zone = ServiceZone::active()->with('city.businesses')->get()->first(fn ($zone) => $zone->contains($this->lat, $this->lng));

        if (! $zone) {
            $this->noService = true;
            $this->city = null;
        } else {
            $this->noService = false;
            $this->city = $zone->city;
        }
    }

    protected function saveAddress()
    {
        $guestToken = request()->cookie('guest_token');

        if (! $this->lat || ! $this->lng || ! $guestToken) {
            return;
        }

        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng' => "{$this->lat},{$this->lng}",
            'key' => config('services.google_maps.key'),
            'language' => 'es',
        ]);

        $data = $response->json();

        if (! isset($data['results'][0])) {
            Log::error('Error API Google:', $data ?? []);

            return;
        }

        $result = $data['results'][0];
        $components = $result['address_components'];

        $calle = $this->extractComponent($components, ['route']);
        $numero = $this->extractComponent($components, ['street_number']);
        $colonia = $this->extractComponent($components, ['sublocality', 'sublocality_level_1', 'neighborhood']);
        $ciudad = $this->extractComponent($components, ['locality', 'administrative_area_level_2']);
        $estado = $this->extractComponent($components, ['administrative_area_level_1']);
        $codigoPostal = $this->extractComponent($components, ['postal_code']);
        $pais = $this->extractComponent($components, ['country']);

        DeliveryAddress::updateOrCreate(
            ['guest_token' => $guestToken],
            [
                'formatted_address' => $result['formatted_address'],
                'street' => $calle,
                'street_number' => $numero,
                'neighborhood' => $colonia,
                'city' => $ciudad,
                'state' => $estado,
                'postal_code' => $codigoPostal,
                'country' => $pais ?? 'México',
                'lat' => $this->lat,
                'lng' => $this->lng,
                'source' => AddressSource::GPS,
                'place_id' => $result['place_id'] ?? null,
            ],
        );
    }

    protected function extractComponent(array $components, array $types): ?string
    {
        foreach ($components as $component) {
            if (! empty(array_intersect($types, $component['types']))) {
                return $component['long_name'];
            }
        }

        return null;
    }

    #[Computed]
    public function tags()
    {
        return Tag::whereIn('name', ['Tacos', 'Hamburguesas', 'Pizza', 'Sushi', 'Mariscos', 'Vegano', 'Postres', 'Café', 'Alitas', 'Desayunos'])->get();
    }

    #[Computed]
    public function filteredBusinesses()
    {
        if (! $this->city) {
            return collect();
        }

        return $this->city->businesses()
            ->active()
            ->orderByDesc('is_open')
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.pages.home');
    }
}