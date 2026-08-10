<?php

namespace App\Filament\Resources\Drivers\Tables;

use App\Enums\DriverAvailability;
use App\Enums\DriverOperationalStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DriversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Split::make([
                        Stack::make([
                            TextColumn::make('user.name')
                                ->searchable()
                                ->sortable()
                                ->weight('bold')
                                ->size(TextSize::Large),

                            TextColumn::make('city.name')
                                ->badge()
                                ->color('gray'),
                        ]),
                    ]),

                    Stack::make([
                        TextColumn::make('user.phone')
                            ->icon('heroicon-m-phone')
                            ->color('gray')
                            ->size(TextSize::ExtraSmall)
                            ->placeholder('Sin teléfono'),

                        TextColumn::make('user.email')
                            ->icon('heroicon-m-envelope')
                            ->color('gray')
                            ->size(TextSize::ExtraSmall),
                    ]),

                    Split::make([
                        TextColumn::make('availability_status')
                            ->badge()
                            ->grow(false),

                        TextColumn::make('operational_status')
                            ->badge()
                            ->grow(false),
                    ]),
                ])->space(3),
            ])
            ->filters([
                SelectFilter::make('city_id')
                    ->label('Ciudad')
                    ->relationship('city', 'name'),

                SelectFilter::make('availability_status')
                    ->label('Disponibilidad')
                    ->options(DriverAvailability::class),

                SelectFilter::make('operational_status')
                    ->label('Estado Operativo')
                    ->options(DriverOperationalStatus::class),
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