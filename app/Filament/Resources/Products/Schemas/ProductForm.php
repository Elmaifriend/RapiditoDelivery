<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información básica')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del producto')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('price')
                            ->label('Precio base')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                    ]),

                Section::make('Descripción')
                    ->schema([
                        Textarea::make('description')
                            ->rows(3),
                    ]),

                Section::make('Imagen')
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Imagen del producto')
                            ->image()
                            ->openable()
                            ->directory('products')
                            ->imagePreviewHeight(200)
                            ->maxSize(2048),
                    ]),

                Section::make('Estado y orden')
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Activo')
                            ->helperText('Si está desactivado, no se muestra en el menú')
                            ->default(true),

                        Toggle::make('is_available')
                            ->label('Disponible')
                            ->helperText('Si está desactivado, no se puede pedir')
                            ->default(true),

                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
