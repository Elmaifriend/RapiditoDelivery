<?php

namespace App\Filament\Resources\ServiceZones\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

// IMPORTANTE: En Filament 4 las acciones viven aquí
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;

class DeliveryZonesRelationManager extends RelationManager
{
    protected static string $relationship = 'deliveryZones';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('delivery_price')
                    ->label('Precio de envío base')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                    
                Forms\Components\TextInput::make('priority')
                    ->label('Prioridad')
                    ->numeric()
                    ->default(0),

                Forms\Components\Textarea::make('polygon_json')
                    ->label('Polígono (JSON)')
                    ->required()
                    ->rows(10)
                    ->columnSpanFull()
                    ->afterStateHydrated(function ($component, $state) {
                        $component->state(json_encode($state, JSON_PRETTY_PRINT));
                    })
                    ->dehydrateStateUsing(function ($state) {
                        return json_decode($state, true);
                    }),
                    
                Forms\Components\Toggle::make('active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('delivery_price')
                    ->label('Precio Base')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Activa')
                    ->boolean(),
            ])
            ->filters([
                // 
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                
                EditAction::make('manageFares')
                    ->label('Gestionar Tarifas')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->modalHeading(fn ($record) => 'Tarifas de salida para: ' . $record->name)
                    ->form([
                        Forms\Components\Repeater::make('outgoingFares')
                            ->relationship('outgoingFares')
                            ->schema([ 
                                Forms\Components\Select::make('to_zone_id')
                                    ->relationship('toZone', 'name')
                                    ->label('Zona de Destino')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('price')
                                    ->label('Precio ($)')
                                    ->required()
                                    ->numeric()
                                    ->columnSpan(1),
                                Forms\Components\Toggle::make('active')
                                    ->label('Activa')
                                    ->default(true)
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->addActionLabel('Agregar nueva tarifa')
                            ->defaultItems(0)
                            ->reorderable(false)
                    ])
                    ->modalWidth('5xl')
                    ->slideOver(),

                DeleteAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}