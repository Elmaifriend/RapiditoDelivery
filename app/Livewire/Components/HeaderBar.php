<?php

namespace App\Livewire\Components;

use App\Models\DeliveryAddress;
use Livewire\Attributes\On;
use Livewire\Component;

class HeaderBar extends Component
{
    public $cityText = 'Detectando...';

    public $streetText = 'ubicando satélites...';

    // Variables para debug
    public ?float $debugLat = null;

    public ?float $debugLng = null;

    public function mount()
    {
        $this->loadAddressFromDb();
    }

    #[On('addressUpdated')]
    public function loadAddressFromDb()
    {
        $guestToken = request()->cookie('guest_token');
        $address = DeliveryAddress::where('guest_token', $guestToken)->latest()->first();

        if ($address) {
            $this->cityText = $address->city ?? 'Ciudad desconocida';

            $calle = $address->street ?? '';
            $colonia = $address->neighborhood ?? '';
            $this->streetText = trim($calle.($colonia ? ', '.$colonia : ''), ', ');

            $this->debugLat = $address->lat;
            $this->debugLng = $address->lng;
        } else {
            $this->cityText = 'Ubicación no disponible';
            $this->streetText = 'Toca aquí para buscar';
        }
    }

    public function render()
    {
        return view('livewire.components.header-bar');
    }
}
