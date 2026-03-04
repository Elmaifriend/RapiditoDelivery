<?php

namespace App\Filament\Resources\CityResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('state')
                    ->label('Estado')
                    ->maxLength(255),

                Forms\Components\TextInput::make('country')
                    ->label('País')
                    ->default('Mexico')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Toggle::make('active')
                    ->label('Activa')
                    ->default(true),
            ]);
    }
}