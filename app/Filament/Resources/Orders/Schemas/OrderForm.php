<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\BusinessDecisionStatus;
use App\Enums\DeliveryOutcome;
use App\Enums\DeliveryStatus;
use App\Enums\OrderLifecycleStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Cliente y Ubicación')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario Registrado')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->nullable(),

                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nombre Cliente')
                            ->required(),

                        Forms\Components\TextInput::make('customer_phone')
                            ->label('Teléfono Cliente')
                            ->tel(),

                        Forms\Components\Select::make('city_id')
                            ->label('Ciudad')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->required(),
                    ])->columns(2),

                Section::make('Negocio y Repartidor')
                    ->schema([
                        Forms\Components\Select::make('business_id')
                            ->label('Restaurante / Negocio')
                            ->relationship('business', 'name')
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('driver_id')
                            ->label('Repartidor')
                            ->relationship('driver', 'name')
                            ->searchable()
                            ->nullable(),
                    ])->columns(2),

                Section::make('Estados del Pedido')
                    ->schema([
                        Forms\Components\Select::make('lifecycle_status')
                            ->label('Estado Ciclo de Vida')
                            ->options(OrderLifecycleStatus::class)
                            ->required(),

                        Forms\Components\Select::make('business_decision_status')
                            ->label('Decisión del Negocio')
                            ->options(BusinessDecisionStatus::class)
                            ->required(),

                        Forms\Components\Select::make('delivery_status')
                            ->label('Estado de Entrega')
                            ->options(DeliveryStatus::class)
                            ->required(),

                        Forms\Components\Select::make('payment_status')
                            ->label('Estado de Pago')
                            ->options(PaymentStatus::class)
                            ->required(),

                        Forms\Components\Select::make('payment_method')
                            ->label('Método de Pago')
                            ->options(PaymentMethod::class)
                            ->required(),

                        Forms\Components\Select::make('delivery_outcome')
                            ->label('Resultado de Entrega')
                            ->options(DeliveryOutcome::class)
                            ->nullable(),
                    ])->columns(3),

                Section::make('Montos')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->required(),

                        Forms\Components\TextInput::make('delivery_fee')
                            ->label('Costo de Envío')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00)
                            ->required(),

                        Forms\Components\TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                    ])->columns(3),

                Section::make('Notas Adicionales')
                    ->schema([
                        Forms\Components\Textarea::make('special_instructions')
                            ->label('Instrucciones Especiales')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('delivery_notes')
                            ->label('Notas de Entrega')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}