<?php

namespace App\Filament\Resources\OptionGroups\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class OptionGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Regla del grupo')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del grupo')
                            ->placeholder('Ej. Sabores, Extras')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('position')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('Restricciones')
                    ->columns(3)
                    ->schema([
                        TextInput::make('min')
                            ->label('Mínimo')
                            ->numeric()
                            ->default(0)
                            ->helperText('Cantidad mínima de opciones a elegir'),

                        TextInput::make('max')
                            ->label('Máximo')
                            ->numeric()
                            ->default(1)
                            ->helperText('Cantidad máxima de opciones a elegir'),

                        Toggle::make('required')
                            ->label('Obligatorio')
                            ->helperText('El cliente debe elegir al menos una opción'),
                    ]),
            ]);
    }
}
