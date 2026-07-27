<?php

namespace App\Filament\Resources\Drivers\Schemas;

use App\Enums\DriverStatus;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class DriverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(6)
            ->components([
                Section::make('Asignación de Usuario')
                    ->description('Selecciona un usuario existente o crea uno nuevo.')
                    ->columnSpan(4)
                    ->schema([
                        ToggleButtons::make('create_new_user')
                            ->label('Tipo de Registro')
                            ->options([
                                false => 'Usuario Existente',
                                true => 'Usuario Nuevo',
                            ])
                            ->colors([
                                false => 'info',
                                true => 'success',
                            ])
                            ->icons([
                                false => 'heroicon-m-user',
                                true => 'heroicon-m-user-plus',
                            ])
                            ->inline()
                            ->live()
                            ->dehydrated(true) // Debe llegar a mutateFormDataBeforeCreate
                            ->default(false),

                        Select::make('user_id')
                            ->label('Buscar Usuario')
                            ->placeholder('Escribe ID, Nombre, Correo o Teléfono...')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search): array => User::query()
                                ->where('id', $search)
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                                ->limit(10)
                                ->pluck('name', 'id')
                                ->mapWithKeys(fn ($name, $id) => [
                                    $id => "ID: {$id} - {$name}",
                                ])
                                ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name
                            )
                            ->required(fn (Get $get): bool => ! (bool) $get('create_new_user'))
                            ->hidden(fn (Get $get): bool => (bool) $get('create_new_user'))
                            ->exists('users', 'id'),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('new_user.name')
                                    ->label('Nombre Completo')
                                    ->required(fn (Get $get): bool => (bool) $get('create_new_user')),

                                TextInput::make('new_user.email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->prefixIcon('heroicon-m-envelope')
                                    ->required(fn (Get $get): bool => (bool) $get('create_new_user'))
                                    ->unique('users', 'email'),

                                TextInput::make('new_user.phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->prefixIcon('heroicon-m-phone')
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
                    ->description('Detalles operativos y de estado.')
                    ->columnSpan(2)
                    ->schema([
                        Select::make('city_id')
                            ->label('Ciudad')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        ToggleButtons::make('status')
                            ->label('Estado Inicial')
                            ->options(DriverStatus::class)
                            ->inline()
                            ->default(DriverStatus::INACTIVE)
                            ->required(),
                    ]),
            ]);
    }
}
