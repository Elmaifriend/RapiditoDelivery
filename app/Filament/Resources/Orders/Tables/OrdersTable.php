<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\BusinessDecisionStatus;
use App\Enums\DeliveryStatus;
use App\Enums\OrderLifecycleStatus;
use App\Enums\PaymentStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->default(fn ($record) => $record->customer_name ?? 'Invitado'),

                Tables\Columns\TextColumn::make('business.name')
                    ->label('Restaurante / Negocio')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('driver.name')
                    ->label('Repartidor')
                    ->sortable()
                    ->placeholder('Sin asignar'),

                Tables\Columns\TextColumn::make('lifecycle_status')
                    ->label('Ciclo de Vida')
                    ->badge()
                    ->color(fn (OrderLifecycleStatus $state): string => match ($state) {
                        OrderLifecycleStatus::PENDING => 'warning',
                        OrderLifecycleStatus::CONFIRMED => 'info',
                        OrderLifecycleStatus::COMPLETED => 'success',
                        OrderLifecycleStatus::CANCELLED => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('business_decision_status')
                    ->label('Decisión Negocio')
                    ->badge()
                    ->color(fn (BusinessDecisionStatus $state): string => match ($state) {
                        BusinessDecisionStatus::PENDING => 'gray',
                        BusinessDecisionStatus::ACCEPTED => 'success',
                        BusinessDecisionStatus::REJECTED => 'danger',
                        BusinessDecisionStatus::PARTIAL_PROPOSAL => 'warning',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('delivery_status')
                    ->label('Envío')
                    ->badge()
                    ->color(fn (DeliveryStatus $state): string => match ($state) {
                        DeliveryStatus::PENDING, DeliveryStatus::WAITING_DRIVER => 'gray',
                        DeliveryStatus::DRIVER_HEADING_TO_RESTAURANT, DeliveryStatus::PICKED_UP => 'warning',
                        DeliveryStatus::ON_THE_WAY => 'info',
                        DeliveryStatus::DELIVERED => 'success',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Pago')
                    ->badge()
                    ->color(fn (PaymentStatus $state): string => match ($state) {
                        PaymentStatus::PENDING => 'gray',
                        PaymentStatus::PAID => 'success',
                        PaymentStatus::REFUND_PENDING => 'warning',
                        PaymentStatus::REFUNDED => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('MXN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('lifecycle_status')
                    ->label('Estado')
                    ->options(OrderLifecycleStatus::class),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Estado de Pago')
                    ->options(PaymentStatus::class),
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