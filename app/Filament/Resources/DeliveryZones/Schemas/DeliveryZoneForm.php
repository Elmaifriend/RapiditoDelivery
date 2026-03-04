<?php

namespace App\Filament\Resources\DeliveryZoneResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class DeliveryZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('service_zone_id')
                    ->label('Service Zone')
                    ->relationship('serviceZone', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('polygon_json')
                    ->label('Polygon JSON (GeoJSON)')
                    ->rows(6)
                    ->required()
                    ->helperText('Formato GeoJSON tipo Polygon')
                    ->dehydrateStateUsing(fn ($state) => json_decode($state, true))
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT)),

                Forms\Components\TextInput::make('delivery_price')
                    ->label('Precio de Envío')
                    ->numeric()
                    ->required()
                    ->prefix('$'),

                Forms\Components\TextInput::make('priority')
                    ->label('Prioridad')
                    ->numeric()
                    ->default(100)
                    ->required(),

                Forms\Components\Toggle::make('active')
                    ->label('Activa')
                    ->default(true),
            ]);
    }
}