<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\DeliveryAddress;

new class extends Component {

    public $cityText = 'Detectando...';
    public $streetText = 'ubicando satélites...';

    // Variables para debug
    public ?float $debugLat = null;
    public ?float $debugLng = null;

    public function mount()
    {
        $this->loadAddressFromDb();
    }

    // El ÚNICO listener que necesitamos
    #[On('addressUpdated')]
    public function loadAddressFromDb()
    {
        $guestToken = request()->cookie('guest_token');
        $address = DeliveryAddress::where('guest_token', $guestToken)->latest()->first();

        if ($address) {
            $this->cityText = $address->city ?? 'Ciudad desconocida';

            $calle = $address->street ?? '';
            $colonia = $address->neighborhood ?? '';
            $this->streetText = trim($calle . ($colonia ? ', ' . $colonia : ''), ', ');

            // Llenamos las variables de debug
            $this->debugLat = $address->lat;
            $this->debugLng = $address->lng;
        } else {
            // Si disparan el evento pero no hay dirección en BD (ej. Denegó ubicación o fuera de zona)
            $this->cityText = 'Ubicación no disponible';
            $this->streetText = 'Toca aquí para buscar';
        }
    }
};
?>

<div
    class="fixed top-0 z-100 flex w-full items-center justify-between rounded-b-2xl bg-white px-8 pt-8 pb-6 cursor-pointer">
    {{-- ETIQUETA DE DEBUG: Se muestra chiquito arriba a la derecha --}}
    @if($debugLat && $debugLng)
    <div
        class="absolute top-1 right-1/2 translate-x-1/2 text-[9px] font-mono text-gray-400 bg-gray-50 px-1 rounded border border-gray-100">
        LAT: {{ $debugLat }} | LNG: {{ $debugLng }}
    </div>
    @endif

    <a class="flex items-center gap-1 text-3xl" wire:navigate href="/">
        <i class="bxf bx-carrot text-red-500"></i>
        <h1 class="font-display font-extrabold text-gray-800">Rapidito</h1>
    </a>

    <a class="flex items-start gap-1.5 text-gray-500" wire:navigate href="/location">
        <div class="flex flex-col gap-1 pt-0.5">
            <span class="text-xl flex items-center gap-1 justify-end font-bold text-gray-800">
                {{ $cityText }}
                <i class="bxf bx-location text-red-400"></i>
            </span>

            @if($streetText)
            <span class="max-w-[220px] truncate text-xs text-gray-400 flex items-center gap-1">
                {{ $streetText }}
                <i class="bxf bx-chevron-down"></i>
            </span>
            @endif
        </div>


    </a>
</div>
