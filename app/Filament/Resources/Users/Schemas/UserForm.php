<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Business;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(6)
            ->components([
                Section::make('Información del Usuario')
                    ->description('Detalles personales y de contacto del usuario.')
                    ->columnSpan(4)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre completo')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->prefixIcon('heroicon-m-envelope')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->prefixIcon('heroicon-m-phone')
                                    ->maxLength(255),

                                TextInput::make('password')
                                    ->label('Contraseña')
                                    ->password()
                                    ->revealable()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Configuración Adicional')
                    ->description('Asignación de roles o negocios.')
                    ->columnSpan(2)
                    ->schema([
                        Select::make('business_id')
                            ->label('Restaurante Asociado')
                            ->options(function () {
                                return Business::with('city')
                                    ->get()
                                    ->groupBy(fn ($business) => $business->city ? $business->city->name : 'Sin ciudad asignada')
                                    ->map(fn ($group) => $group->pluck('name', 'id'))
                                    ->toArray();
                            })
                            ->searchable()
                            ->nullable(),
                    ]),
            ]);
    }
}
