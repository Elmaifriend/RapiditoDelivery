<?php

namespace App\Livewire\Pages;

use App\Enums\DriverAvailability;
use App\Models\Driver;
use Livewire\Component;
use Livewire\Attributes\Locked;

class DriverProfile extends Component
{
    #[Locked]
    public int $driverId;

    public function mount(Driver $driver)
    {
        $this->driverId = $driver->id;
    }

    public function toggleAvailability()
    {
        $driver = Driver::findOrFail($this->driverId);

        // Alternar entre OFFLINE y ONLINE usando el enum correcto
        $newAvailability = ($driver->availability_status === DriverAvailability::OFFLINE)
            ? DriverAvailability::ONLINE
            : DriverAvailability::OFFLINE;

        $driver->update([
            'availability_status' => $newAvailability,
        ]);

        // Emitimos el evento para notificar la actualización del estado
        $this->dispatch('driver-status-updated');
    }

    public function render()
    {
        $driver = Driver::with(['user', 'city'])->findOrFail($this->driverId);

        return view('livewire.pages.driver-profile', [
            'driver' => $driver,
            'user' => $driver->user,
            'isAvailable' => $driver->availability_status === DriverAvailability::ONLINE,
        ])->layout('layouts.app');
    }
}