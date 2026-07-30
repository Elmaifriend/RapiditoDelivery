<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Split::make([
                        Stack::make([
                            TextColumn::make('name')
                                ->searchable()
                                ->sortable()
                                ->weight('bold')
                                ->size(TextSize::Large),

                            TextColumn::make('email')
                                ->icon('heroicon-m-envelope')
                                ->color('gray'),
                        ]),
                    ]),

                    Stack::make([
                        TextColumn::make('phone')
                            ->icon('heroicon-m-phone')
                            ->color('gray')
                            ->size(TextSize::ExtraSmall)
                            ->placeholder('Sin teléfono'),

                        TextColumn::make('business.name')
                            ->label('Restaurante')
                            ->icon('heroicon-m-building-storefront')
                            ->color('gray')
                            ->size(TextSize::ExtraSmall)
                            ->placeholder('Sin restaurante asignado'),
                    ]),
                ])->space(3),
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
            ])
            ->defaultSort('created_at', 'desc')
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ]);
    }
}
