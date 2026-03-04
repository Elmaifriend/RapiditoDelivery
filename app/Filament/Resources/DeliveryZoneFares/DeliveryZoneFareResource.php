<?php

namespace App\Filament\Resources\DeliveryZoneFares;

use App\Filament\Resources\DeliveryZoneFares\Pages\CreateDeliveryZoneFare;
use App\Filament\Resources\DeliveryZoneFares\Pages\EditDeliveryZoneFare;
use App\Filament\Resources\DeliveryZoneFares\Pages\ListDeliveryZoneFares;
use App\Filament\Resources\DeliveryZoneFares\Schemas\DeliveryZoneFareForm;
use App\Filament\Resources\DeliveryZoneFares\Tables\DeliveryZoneFaresTable;
use App\Models\DeliveryZoneFare;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeliveryZoneFareResource extends Resource
{
    protected static ?string $model = DeliveryZoneFare::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Tarifas Entre Zonas de Servicio';

    public static function form(Schema $schema): Schema
    {
        return DeliveryZoneFareForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveryZoneFaresTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeliveryZoneFares::route('/'),
            'create' => CreateDeliveryZoneFare::route('/create'),
            'edit' => EditDeliveryZoneFare::route('/{record}/edit'),
        ];
    }
}
