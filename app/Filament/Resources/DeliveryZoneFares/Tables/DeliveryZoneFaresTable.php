<?php

namespace App\Filament\Resources\DeliveryZoneFares\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;

class DeliveryZoneFaresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fromZone.name')
                    ->label('Origen')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('toZone.name')
                    ->label('Destino')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('MXN')
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
