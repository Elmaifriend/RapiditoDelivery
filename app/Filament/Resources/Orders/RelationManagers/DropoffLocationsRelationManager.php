<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms;

class DropoffLocationsRelationManager extends RelationManager
{
    protected static string $relationship = 'dropoffLocations';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('lat')
                    ->label('Latitud')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('lng')
                    ->label('Longitud')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('source')
                    ->label('Origen')
                    ->required(),

                Forms\Components\Toggle::make('confirmed')
                    ->label('Confirmada')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('lat')
            ->columns([
                Tables\Columns\TextColumn::make('lat')
                    ->label('Lat')
                    ->sortable(),

                Tables\Columns\TextColumn::make('lng')
                    ->label('Lng')
                    ->sortable(),

                Tables\Columns\TextColumn::make('source')
                    ->label('Origen')
                    ->badge(),

                Tables\Columns\IconColumn::make('confirmed')
                    ->label('Confirmada')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Fecha'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
