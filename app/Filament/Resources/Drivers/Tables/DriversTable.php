<?php

namespace App\Filament\Resources\Drivers\Tables;

use App\Enums\DriverStatus;
use Filament\Actions\BulkActionGroup;   // <-- CAMBIO AQUÍ
use Filament\Actions\DeleteBulkAction; // <-- CAMBIO AQUÍ
use Filament\Actions\EditAction;       // <-- CAMBIO AQUÍ
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DriversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Correo')
                    ->searchable(),

                TextColumn::make('user.phone')
                    ->label('Teléfono')
                    ->searchable(),

                TextColumn::make('city.name')
                    ->label('Ciudad')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('city_id')
                    ->label('Ciudad')
                    ->relationship('city', 'name'),

                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(DriverStatus::class),
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