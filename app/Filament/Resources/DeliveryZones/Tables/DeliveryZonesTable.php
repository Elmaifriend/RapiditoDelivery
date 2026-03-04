<?php

namespace App\Filament\Resources\DeliveryZones\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;

class DeliveryZonesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serviceZone.name')
                    ->label('Service Zone')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Zona')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('delivery_price')
                    ->label('Precio')
                    ->money('MXN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->sortable(),

                Tables\Columns\IconColumn::make('active')
                    ->label('Activa')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
