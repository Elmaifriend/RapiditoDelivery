<?php

namespace App\Filament\Resources\ServiceZones\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;

class ServiceZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('city_id')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Toggle::make('active')
                    ->default(true),

                Forms\Components\Toggle::make('debug')
                    ->default(false),

                Forms\Components\Textarea::make('polygon')
                    ->required()
                    ->rows(10)
                    ->columnSpanFull()
                    ->afterStateHydrated(function ($component, $state) {
                        $component->state(json_encode($state, JSON_PRETTY_PRINT));
                    })
                    ->dehydrateStateUsing(function ($state) {
                        return json_decode($state, true);
                    }),
            ]);
    }
}
