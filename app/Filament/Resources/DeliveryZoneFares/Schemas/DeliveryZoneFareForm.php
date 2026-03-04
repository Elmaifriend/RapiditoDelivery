<?php

namespace App\Filament\Resources\DeliveryZoneFareResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class DeliveryZoneFareForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Forms\Components\Select::make('from_zone_id')
                    ->label('Zona Origen')
                    ->relationship('fromZone', 'name')
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->rules([
                        fn ($get) => Rule::notIn([$get('to_zone_id')]),
                    ]),

                Forms\Components\Select::make('to_zone_id')
                    ->label('Zona Destino')
                    ->relationship('toZone', 'name')
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->rules([
                        fn ($get) => Rule::notIn([$get('from_zone_id')]),
                    ]),

                Forms\Components\TextInput::make('price')
                    ->label('Precio')
                    ->numeric()
                    ->required()
                    ->prefix('$'),

                Forms\Components\Toggle::make('active')
                    ->label('Activa')
                    ->default(true),
            ]);
    }
}