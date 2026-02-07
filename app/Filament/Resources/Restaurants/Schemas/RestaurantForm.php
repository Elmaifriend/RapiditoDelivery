<?php

namespace App\Filament\Resources\Restaurants\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;

use Illuminate\Support\Str;

class RestaurantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)    
            ->components([
                Section::make('Banner')
                    ->schema([
                        FileUpload::make('banner_path')
                            ->label('Banner')
                            ->image()
                            ->directory('restaurants/banners'),
                    ]),

                Section::make('Información general')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->directory('restaurants/logos'),

                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn ($state, callable $set) =>
                                            $set('slug', Str::slug($state))
                                    ),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),

                                Select::make('category_id')
                                    ->label('Categoría')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('tags')
                                    ->label('Tags')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ]),

                Section::make('Ubicación')
                    ->columns(2)
                    ->schema([
                        TextInput::make('address')
                            ->label('Dirección'),

                        TextInput::make('city')
                            ->label('Ciudad'),

                        TextInput::make('state')
                            ->label('Estado / Provincia'),

                        TextInput::make('postal_code')
                            ->label('Código postal'),

                        TextInput::make('country')
                            ->label('País')
                            ->default('México'),

                        TextInput::make('google_maps_url')
                            ->label('Google Maps URL')
                            ->url(),

                        TextInput::make('lat')
                            ->label('Latitud')
                            ->numeric(),

                        TextInput::make('lng')
                            ->label('Longitud')
                            ->numeric(),

                        FileUpload::make('reference_image')
                            ->columnSpanFull()
                            ->label('Imagen de referencia')
                            ->image()
                            ->directory('restaurants/references'),
                    ]),

                Section::make('Configuración')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activo',
                                'inactive' => 'Inactivo',
                            ])
                            ->required(),

                        Toggle::make('is_open')
                            ->label('Abierto'),

                        Toggle::make('accepts_delivery')
                            ->label('Acepta delivery'),

                        Toggle::make('accepts_pickup')
                            ->label('Acepta pickup'),
                    ]),
            ]);
    }
}
