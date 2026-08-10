<?php

namespace App\Livewire\Pages;

use App\Models\Business;
use App\Models\City;
use App\Models\Driver;
use App\Enums\DriverAvailability;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Enums\BusinessStatus;

#[Layout('layouts.app')]
#[Title('Gestión por Ciudad')]
class CityManagement extends Component
{
    #[Url(as: 'city', keep: true)]
    public ?int $selectedCityId = null;

    public string $search = '';
    public string $activeTab = 'repartidores';

    public function mount(mixed $city = null): void
    {
        if ($city) {
            $this->selectedCityId = is_numeric($city) ? (int) $city : City::where('slug', $city)->first()?->id;
        }

        if (!$this->selectedCityId) {
            $this->selectedCityId = City::where('active', true)->first()?->id;
        }
    }

    public function selectCity(int $cityId): void
    {
        $this->selectedCityId = $cityId;
        $this->search = '';
    }

    public function changeTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ==========================================
    // MÉTODOS PARA RESTAURANTES (BUSINESS)
    // ==========================================

    /**
     * Función vacía lista para integrar la lógica de envío de link de panel al restaurante.
     */
    public function sendBusinessPanelLink(int $businessId): void
    {
        $business = Business::find($businessId);
        if (!$business) return;

        // TODO: Agregar lógica para enviar el enlace al restaurante (WhatsApp, Email, etc.)

        session()->flash('message', "Enlace enviado al restaurante {$business->name}.");
    }

    /**
     * Alterna el estado del restaurante entre Abierto e Indispuesto/Cerrado (is_open).
     */
    public function toggleBusinessOpen(int $businessId): void
    {
        $business = Business::find($businessId);
        if ($business) {
            $business->update([
                'is_open' => !$business->is_open,
            ]);
            
            $status = $business->is_open ? 'abierto' : 'cerrado';
            session()->flash('message', "El restaurante {$business->name} ahora está {$status}.");
        }
    }

    /**
     * Alterna la disponibilidad para envíos a domicilio (accepts_delivery).
     */
    public function toggleBusinessDelivery(int $businessId): void
    {
        $business = Business::find($businessId);
        if ($business) {
            $business->update([
                'accepts_delivery' => !$business->accepts_delivery,
            ]);

            $status = $business->accepts_delivery ? 'habilitado' : 'deshabilitado';
            session()->flash('message', "Envíos a domicilio {$status}s para {$business->name}.");
        }
    }


    public function toggleBusinessStatus(int $businessId): void
    {
        $business = Business::find($businessId);
        
        if ($business) {
            // Comparación y asignación utilizando los casos del Enum
            $newStatus = $business->status === BusinessStatus::ACTIVE 
                ? BusinessStatus::INACTIVE 
                : BusinessStatus::ACTIVE;

            $business->update([
                'status' => $newStatus,
            ]);

            $label = $newStatus === BusinessStatus::ACTIVE 
                ? 'activado y visible' 
                : 'deshabilitado/oculto';

            session()->flash('message', "El restaurante {$business->name} ha sido {$label}.");
        }
    }

    // ==========================================
    // MÉTODOS PARA REPARTIDORES (DRIVER)
    // ==========================================

    /**
     * Función vacía lista para integrar la lógica de envío de link de panel al repartidor.
     */
    public function sendDriverPanelLink(int $driverId): void
    {
        $driver = Driver::with('user')->find($driverId);
        if (!$driver) return;

        // TODO: Agregar lógica para enviar el enlace al repartidor (WhatsApp, Email, etc.)

        session()->flash('message', "Enlace enviado al repartidor " . ($driver->user->name ?? "#{$driver->id}"));
    }

    /**
     * Alterna la disponibilidad del repartidor entre Disponible (online) e Indisponible (offline).
     */
    public function toggleDriverAvailability(int $driverId): void
    {
        $driver = Driver::find($driverId);
        if ($driver) {
            $currentStatus = is_object($driver->availability_status) && property_exists($driver->availability_status, 'value')
                ? $driver->availability_status->value
                : (string) $driver->availability_status;

            $newStatus = ($currentStatus === 'online') ? DriverAvailability::OFFLINE : DriverAvailability::ONLINE;

            $driver->update([
                'availability_status' => $newStatus,
            ]);

            session()->flash('message', "Estado de disponibilidad del repartidor actualizado.");
        }
    }

    /**
     * Alterna si el repartidor está activo en la plataforma o no (is_active).
     */
    public function toggleDriverActive(int $driverId): void
    {
        $driver = Driver::find($driverId);
        if ($driver) {
            $driver->update([
                'is_active' => !$driver->is_active,
            ]);

            $status = $driver->is_active ? 'activado' : 'desactivado';
            session()->flash('message', "Repartidor {$status} en la plataforma.");
        }
    }

    #[Computed]
    public function cities()
    {
        return City::query()
            ->where('active', true)
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function selectedCity()
    {
        return $this->selectedCityId ? City::find($this->selectedCityId) : null;
    }

    #[Computed]
    public function drivers()
    {
        if (!$this->selectedCityId) {
            return collect();
        }

        return Driver::with('user')
            ->where('city_id', $this->selectedCityId)
            ->latest()
            ->get();
    }

    #[Computed]
    public function businesses()
    {
        if (!$this->selectedCityId) {
            return collect();
        }

        return Business::with('category')
            ->where('city_id', $this->selectedCityId)
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.pages.city-management');
    }
}