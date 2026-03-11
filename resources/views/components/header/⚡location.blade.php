<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\DeliveryAddress;

new class extends Component {
    
    public $locationText = 'Detectando ubicación...';

    public function mount()
    {
        $this->loadAddressFromDb();
    }

    // 1. Carga desde la BD (Ideal para cuando navegas a otras páginas)
    #[On('addressUpdated')]
    public function loadAddressFromDb()
    {
        $guestToken = request()->cookie('guest_token');
        $address = DeliveryAddress::where('guest_token', $guestToken)->latest()->first();

        if ($address && $address->city) {
            $this->locationText = $address->city;
        }
    }

    // 2. RECIBE EL TEXTO DIRECTO DESDE HOME (¡Súper rápido!)
    #[On('updateHeaderLocation')]
    public function setLocationText($text)
    {
        $this->locationText = $text;
    }
};
?>

<div 
    wire:navigate
    href="/location"
    class="flex w-full items-center justify-between rounded-b-2xl bg-white p-4 cursor-pointer shadow-sm"
>
    <div>
        <div class="flex items-center gap-1 text-3xl">
            <i class="bxf bx-carrot text-red-500"></i>
            <h1 class="font-display font-extrabold">Rapidito</h1>
        </div>

        <div class="flex items-center gap-1 text-gray-500 mt-1">
            <p class="flex items-center gap-1 text-xs font-medium">
                <i class="bxf bx-location text-red-400"></i>
                
                {{-- Ahora solo imprimimos el texto reactivo --}}
                <span class="truncate max-w-[200px]">{{ $locationText }}</span>
                
            </p>
            <i class="bxf bx-chevron-down text-xs"></i>
        </div>
    </div>
</div>