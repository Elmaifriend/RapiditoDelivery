<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255),

                // Selector del restaurante con búsqueda por nombre e ID
                Select::make('business_id')
                    ->relationship(
                        name: 'business', 
                        titleAttribute: 'name'
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "[ID: {$record->id}] {$record->name}")
                    ->searchable(['id', 'name']) // Permite buscar por ID o Nombre en la DB
                    ->preload()                  // Opcional: precarga registros para rapidez
                    ->nullable()
                    ->label('Restaurante Asociado'),
            ]);
    }
}