<?php

namespace App\Filament\Resources\OptionGroups\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class OptionGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Grupo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('min')
                    ->label('Min'),

                TextColumn::make('max')
                    ->label('Max'),

                IconColumn::make('required')
                    ->label('Obligatorio')
                    ->boolean(),

                TextColumn::make('position')
                    ->label('Orden')
                    ->sortable(),
            ])
            ->filters([
                // luego puedes agregar filtros por required, etc.
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
