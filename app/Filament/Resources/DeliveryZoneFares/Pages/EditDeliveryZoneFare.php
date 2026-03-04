<?php

namespace App\Filament\Resources\DeliveryZoneFares\Pages;

use App\Filament\Resources\DeliveryZoneFares\DeliveryZoneFareResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryZoneFare extends EditRecord
{
    protected static string $resource = DeliveryZoneFareResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
