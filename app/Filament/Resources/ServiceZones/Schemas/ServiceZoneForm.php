<?php

namespace App\Filament\Resources\ServiceZones\Schemas;

use App\Filament\Forms\Components\PolygonMapPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Información de la Zona')
                    ->description('Define los límites geográficos y la configuración de esta zona de servicio.')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre de la Zona')
                            ->placeholder('Ej: Zona Centro, Polígono Industrial...')
                            ->required()
                            ->maxLength(255),

                        Select::make('city_id')
                            ->label('Ciudad Base')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Section::make('Definición Geográfica')
                            ->description('Modifica visualmente los límites de esta zona de servicio en el mapa.')
                            ->columnSpanFull()
                            ->schema([
                                PolygonMapPicker::make('polygon')
                                    ->label('Mapa de la Zona')
                                    ->height(600)
                                    ->zoom(13)
                                    ->center([18.4861, -69.9312]),

                                Grid::make(4)
                                    ->visible(fn ($record) => $record !== null)
                                    ->schema([
                                        Placeholder::make('bbox_min_lat')
                                            ->label('Min Lat')
                                            ->content(fn ($record) => $record?->bbox_min_lat)
                                            ->extraAttributes([
                                                'style' => 'font-family: monospace;',
                                            ]),

                                        Placeholder::make('bbox_max_lat')
                                            ->label('Max Lat')
                                            ->content(fn ($record) => $record?->bbox_max_lat)
                                            ->extraAttributes([
                                                'style' => 'font-family: monospace;',
                                            ]),

                                        Placeholder::make('bbox_min_lng')
                                            ->label('Min Lng')
                                            ->content(fn ($record) => $record?->bbox_min_lng)
                                            ->extraAttributes([
                                                'style' => 'font-family: monospace;',
                                            ]),

                                        Placeholder::make('bbox_max_lng')
                                            ->label('Max Lng')
                                            ->content(fn ($record) => $record?->bbox_max_lng)
                                            ->extraAttributes([
                                                'style' => 'font-family: monospace;',
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->grow(true),

                Section::make('Configuración Adicional')
                    ->description('Ajusta el estatus operativo y otras configuraciones avanzadas de esta zona de servicio.')
                    ->columnSpan(1)
                    ->schema([
                        ToggleButtons::make('active')
                            ->label('Estatus')
                            ->boolean()
                            ->options([
                                true => 'Operativa',
                                false => 'Inactiva',
                            ])
                            ->colors([
                                true => 'success',
                                false => 'danger',
                            ])
                            ->icons([
                                true => 'heroicon-m-check-circle',
                                false => 'heroicon-m-x-circle',
                            ])
                            ->inline()
                            ->default(true),

                        ToggleButtons::make('debug')
                            ->label('Modo Depuración')
                            ->boolean()
                            ->options([
                                true => 'Activo',
                                false => 'Apagado',
                            ])
                            ->colors([
                                true => 'warning',
                                false => 'gray',
                            ])
                            ->icons([
                                true => 'heroicon-m-bug-ant',
                                false => 'heroicon-m-no-symbol',
                            ])
                            ->inline()
                            ->default(false),

                        Placeholder::make('delivery_zones_count')
                            ->label('Zonas de Entrega')
                            ->content(fn ($record) => $record?->deliveryZones()->count() ?? 0),
                    ]),
            ]);
    }
}
