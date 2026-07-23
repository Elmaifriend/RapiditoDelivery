<?php

namespace App\Filament\Resources\Drivers\Schemas;

use App\Enums\DriverStatus;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class DriverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Asignación de Usuario')
                    ->description('Selecciona un usuario existente o crea uno nuevo.')
                    ->columns(1)
                    ->schema([
                        Toggle::make('create_new_user')
                            ->label('¿Es un usuario completamente nuevo?')
                            ->live()
                            ->dehydrated(true) // Debe llegar a mutateFormDataBeforeCreate
                            ->default(false),

                        Select::make('user_id')
                            ->label('Buscar Usuario')
                            ->placeholder('Escribe ID, Nombre, Correo o Teléfono...')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search): array => 
                                User::query()
                                    ->where('id', $search)
                                    ->orWhere('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%")
                                    ->limit(10)
                                    ->pluck('name', 'id')
                                    ->mapWithKeys(fn ($name, $id) => [
                                        $id => "ID: {$id} - {$name}"
                                    ])
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => 
                                User::find($value)?->name
                            )
                            ->required(fn (Get $get): bool => ! (bool) $get('create_new_user'))
                            ->hidden(fn (Get $get): bool => (bool) $get('create_new_user'))
                            ->exists('users', 'id'),

                        Group::make()
                            ->columns(1)
                            ->schema([
                                TextInput::make('new_user.name')
                                    ->label('Nombre Completo')
                                    ->required(fn (Get $get): bool => (bool) $get('create_new_user')),

                                TextInput::make('new_user.email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->required(fn (Get $get): bool => (bool) $get('create_new_user'))
                                    ->unique('users', 'email'),

                                TextInput::make('new_user.phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->required(fn (Get $get): bool => (bool) $get('create_new_user')),

                                TextInput::make('new_user.password')
                                    ->label('Contraseña')
                                    ->password()
                                    ->revealable()
                                    ->required(fn (Get $get): bool => (bool) $get('create_new_user')),
                            ])
                            ->visible(fn (Get $get): bool => (bool) $get('create_new_user')),
                    ]),

                Section::make('Información del Repartidor')
                    ->columns(1)
                    ->schema([
                        Select::make('city_id')
                            ->label('Ciudad')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('status')
                            ->label('Estado Inicial')
                            ->options(DriverStatus::class)
                            ->default(DriverStatus::INACTIVE)
                            ->required(),
                    ]),
            ]);
    }
}