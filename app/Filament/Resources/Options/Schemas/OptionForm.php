<?php

namespace App\Filament\Resources\Options\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class OptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Opción')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre de la opción')
                            ->placeholder('Ej. Pepperoni, Extra queso')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('price')
                            ->label('Precio adicional')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                    ]),

                Section::make('Estado')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Activa')
                            ->default(true),

                        TextInput::make('order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
