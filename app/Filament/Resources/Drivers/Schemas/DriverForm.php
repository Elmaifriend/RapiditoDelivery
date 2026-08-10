<?php

namespace App\Filament\Resources\Drivers\Schemas;

use App\Enums\CountryCode;
use App\Enums\DayOfWeek;
use App\Enums\DriverAvailability;
use App\Enums\DriverOperationalStatus;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
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
                            ->dehydrated(true)
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

                                Grid::make(3)
                                    ->columnSpan(2)
                                    ->schema([
                                        Select::make('new_user.country_code')
                                            ->label('Lada')
                                            ->options(collect(CountryCode::cases())->mapWithKeys(fn ($code) => [
                                                $code->value => "{$code->flag()} {$code->dialCode()}",
                                            ]))
                                            ->default(CountryCode::MX->value)
                                            ->selectablePlaceholder(false)
                                            ->searchable()
                                            ->required(fn (Get $get): bool => (bool) $get('create_new_user')),

                                        TextInput::make('new_user.phone')
                                            ->label('Teléfono')
                                            ->tel()
                                            ->placeholder('665 123 4567')
                                            ->columnSpan(2)
                                            ->required(fn (Get $get): bool => (bool) $get('create_new_user')),
                                    ]),

                                TextInput::make('new_user.password')
                                    ->label('Contraseña')
                                    ->password()
                                    ->revealable()
                                    ->columnSpan(3)
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

                        ToggleButtons::make('availability_status')
                            ->label('Disponibilidad (Turno)')
                            ->options(DriverAvailability::class)
                            ->inline()
                            ->default(DriverAvailability::OFFLINE)
                            ->required(),

                        Select::make('operational_status')
                            ->label('Estado Operativo')
                            ->options(DriverOperationalStatus::class)
                            ->default(DriverOperationalStatus::IDLE)
                            ->required(),
                    ]),

                Section::make('Horario de Trabajo')
                    ->description('Gestiona los días y turnos de disponibilidad.')
                    ->columnSpan(6)
                    ->schema([
                        Repeater::make('schedules')
                            ->relationship('schedules')
                            ->schema([
                                Select::make('day_of_week')
                                    ->label('Día de la Semana')
                                    ->options(collect(DayOfWeek::cases())->mapWithKeys(fn ($day) => [
                                        $day->value => $day->label(),
                                    ]))
                                    ->enum(DayOfWeek::class)
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                TimePicker::make('start_time')
                                    ->label('Hora de Inicio')
                                    ->seconds(false)
                                    ->default('08:00')
                                    ->required(),

                                TimePicker::make('end_time')
                                    ->label('Hora de Fin')
                                    ->seconds(false)
                                    ->default('20:00')
                                    ->required(),

                                Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar Día / Horario')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                if (! isset($state['day_of_week'])) {
                                    return 'Nuevo Horario';
                                }

                                $dayEnum = $state['day_of_week'] instanceof DayOfWeek
                                    ? $state['day_of_week']
                                    : DayOfWeek::tryFrom((int) $state['day_of_week']);

                                $dayLabel = $dayEnum?->label() ?? 'Día no seleccionado';
                                $startTime = $state['start_time'] ?? '--:--';
                                $endTime = $state['end_time'] ?? '--:--';

                                return "{$dayLabel} ({$startTime} - {$endTime})";
                            }),
                    ]),
            ]);
    }
}