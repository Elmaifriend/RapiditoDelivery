<?php

namespace App\Filament\Resources\DeliveryZoneFares\Pages;

use App\Filament\Resources\DeliveryZoneFares\DeliveryZoneFareResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryZoneFares extends ListRecords
{
    protected static string $resource = DeliveryZoneFareResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
