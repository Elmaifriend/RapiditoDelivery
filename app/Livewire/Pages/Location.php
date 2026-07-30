<?php

namespace App\Livewire\Pages;

use App\Enums\AddressSource;
use App\Models\DeliveryAddress;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Buscar Dirección')]
class Location extends Component
{
    public $lat;

    public $lng;

    public $googleResult = null;

    public $formattedAddress;

    #[Url]
    public string $mode = 'settings';

    public function mount()
    {
        $guestToken = request()->cookie('guest_token');
        $userId = auth()->id();

        $existingAddress = null;
        if ($userId) {
            $existingAddress = DeliveryAddress::where('user_id', $userId)->latest('last_used_at')->first();
        } elseif ($guestToken) {
            $existingAddress = DeliveryAddress::where('guest_token', $guestToken)->latest('last_used_at')->first();
        }

        if ($existingAddress) {
            $this->lat = $existingAddress->lat;
            $this->lng = $existingAddress->lng;
            $this->formattedAddress = $existingAddress->formatted_address;

            $this->googleResult = [
                'formatted_address' => $existingAddress->formatted_address,
                'place_id' => $existingAddress->place_id,
                'address_components' => [],
            ];
        }
    }

    public function updateLocation($lat, $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->reverseGeocode();
    }

    public function reverseGeocode()
    {
        if (! $this->lat || ! $this->lng) {
            return;
        }

        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng' => "{$this->lat},{$this->lng}",
            'key' => config('services.google_maps.key'),
            'language' => 'es',
        ]);

        $data = $response->json();

        if (isset($data['results'][0])) {
            $this->googleResult = $data['results'][0];
            $this->formattedAddress = $this->googleResult['formatted_address'];
        } else {
            $this->googleResult = null;
            $this->formattedAddress = 'Ubicación desconocida';
        }
    }

    public function save()
    {
        if (! $this->lat || ! $this->lng || ! $this->googleResult) {
            return;
        }

        $guestToken = request()->cookie('guest_token');
        $components = $this->googleResult['address_components'] ?? [];

        $calle = $this->extractComponent($components, ['route']);
        $numero = $this->extractComponent($components, ['street_number']);
        $colonia = $this->extractComponent($components, ['sublocality', 'sublocality_level_1', 'neighborhood']);
        $ciudad = $this->extractComponent($components, ['locality', 'administrative_area_level_2']);
        $estado = $this->extractComponent($components, ['administrative_area_level_1']);
        $pais = $this->extractComponent($components, ['country']);

        $userId = auth()->id();

        $existingAddress = null;
        if ($userId) {
            $existingAddress = DeliveryAddress::where('user_id', $userId)->latest('last_used_at')->first();
        } elseif ($guestToken) {
            $existingAddress = DeliveryAddress::where('guest_token', $guestToken)->latest('last_used_at')->first();
        }

        $ciudad = $ciudad ?: $existingAddress?->city;
        $estado = $estado ?: $existingAddress?->state;
        $pais = $pais ?: ($existingAddress?->country ?? 'México');

        if (! $ciudad || ! $estado) {
            $this->addError('location', 'No se pudo determinar la ciudad o estado. Por favor, selecciona otro punto en el mapa o busca una dirección.');

            return;
        }

        $addressLine = null;
        if (! empty($components)) {
            $addressLine = trim(($calle ? $calle : '').' '.($numero ? $numero : ''));
            if ($colonia) {
                $addressLine = trim($addressLine.', '.$colonia);
            }
        }

        if (empty($addressLine)) {
            $addressLine = $existingAddress?->address_line ?? $this->formattedAddress;
        }

        DeliveryAddress::updateOrCreate(
            [
                'guest_token' => $guestToken,
                'user_id' => $userId,
            ],
            [
                'formatted_address' => $this->formattedAddress,
                'address_line' => $addressLine ?: $this->formattedAddress,
                'city' => $ciudad,
                'state' => $estado,
                'country' => $pais ?? 'México',
                'lat' => $this->lat,
                'lng' => $this->lng,
                'source' => AddressSource::WEB,
                'place_id' => $this->googleResult['place_id'] ?? null,
                'last_used_at' => now(),
            ]
        );

        if ($this->mode === 'checkout') {
            $this->redirect('/checkout', navigate: true);
        } else {
            $this->dispatch('addressUpdated');
            $this->redirect('/', navigate: true);
        }
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

    public function render()
    {
        return view('livewire.pages.location');
    }
}
