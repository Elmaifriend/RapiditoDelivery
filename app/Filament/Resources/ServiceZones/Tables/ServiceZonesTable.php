<?php

namespace App\Filament\Resources\ServiceZones\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;

class ServiceZonesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city.name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('active')
                    ->boolean(),

                Tables\Columns\IconColumn::make('debug')
                    ->boolean(),

                Tables\Columns\TextColumn::make('min_lat'),
                Tables\Columns\TextColumn::make('max_lat'),
                Tables\Columns\TextColumn::make('min_lng'),
                Tables\Columns\TextColumn::make('max_lng'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
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
