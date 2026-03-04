<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;
use App\Enums\OrderStatus;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurante')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('driver.name')
                    ->label('Repartidor')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        OrderStatus::DRAFT->value => 'gray',
                        OrderStatus::CONFIRMING_ORDER->value => 'gray',
                        OrderStatus::CONFIRMED->value => 'primary',
                        OrderStatus::CONFIRMING_LOCATION->value => 'warning',
                        OrderStatus::LOCATION_CONFIRMED->value => 'info',
                        OrderStatus::RESTAURANT_PENDING->value => 'warning',
                        OrderStatus::RESTAURANT_ACCEPTED->value => 'primary',
                        OrderStatus::PREPARING->value => 'warning',
                        OrderStatus::READY_FOR_PICKUP->value => 'info',
                        OrderStatus::ON_THE_WAY->value => 'success',
                        OrderStatus::DELIVERED->value => 'success',
                        OrderStatus::CANCELLED->value => 'danger',
                        OrderStatus::RESTAURANT_REJECTED->value => 'danger',
                        OrderStatus::PARTIAL_UNAVAILABLE->value => 'warning',
                        OrderStatus::REFUND_PENDING->value => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('MXN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->colors([
                        'gray' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'warning' => 'refunded',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Fecha')
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
