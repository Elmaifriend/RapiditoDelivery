<?php

namespace App\Filament\Resources\Restaurants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class RestaurantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->circular()
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->sortable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->searchable(),

                Tables\Columns\TextColumn::make('country')
                    ->label('País')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_open')
                    ->label('Abierto')
                    ->boolean(),

                Tables\Columns\IconColumn::make('accepts_delivery')
                    ->label('Delivery')
                    ->boolean(),

                Tables\Columns\IconColumn::make('accepts_pickup')
                    ->label('Pickup')
                    ->boolean(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Activo',
                        'inactive' => 'Inactivo',
                    ]),

                SelectFilter::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name'),

                Tables\Filters\TernaryFilter::make('is_open')
                    ->label('Abierto'),

                Tables\Filters\TernaryFilter::make('accepts_delivery')
                    ->label('Delivery'),

                Tables\Filters\TernaryFilter::make('accepts_pickup')
                    ->label('Pickup'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
