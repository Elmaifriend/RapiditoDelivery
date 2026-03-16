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
    wire:navigate
    href="/location"
    class="flex w-full items-center justify-between rounded-b-2xl bg-white p-4 cursor-pointer shadow-sm relative"
>
    {{-- ETIQUETA DE DEBUG: Se muestra chiquito arriba a la derecha --}}
    @if($debugLat && $debugLng)
        <div class="absolute top-1 right-2 text-[9px] font-mono text-gray-400 bg-gray-50 px-1 rounded border border-gray-100">
            LAT: {{ $debugLat }} | LNG: {{ $debugLng }}
        </div>
    @endif

    <div>
        <div class="flex items-center gap-1 text-3xl mt-2">
            <i class="bxf bx-carrot text-red-500"></i>
            <h1 class="font-display font-extrabold text-gray-800">Rapidito</h1>
        </div>

        <div class="flex items-start gap-1.5 text-gray-500 mt-2">
            <i class="bxf bx-location text-red-400 mt-0.5"></i>
            
            <div class="flex flex-col leading-tight">
                <span class="text-sm font-bold text-gray-800">{{ $cityText }}</span>
                
                @if($streetText)
                    <span class="max-w-[220px] truncate text-xs font-medium text-gray-500">
                        {{ $streetText }}
                    </span>
                @endif
            </div>

            <i class="bxf bx-chevron-down text-xs mt-1 ml-1"></i>
        </div>
    </div>
</div>