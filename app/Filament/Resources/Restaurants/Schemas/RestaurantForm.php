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
                            ->disk("r2")
                            ->openable()
                            ->visibility("private")
                            ->directory('restaurants/banners')
                            ->imageEditor()
                            ->imageEditorAspectRatioOptions([
                                '3:1',
                            ])
                            ->imageAspectRatio('3:1')
                            ->automaticallyOpenImageEditorForAspectRatio()
                            ->automaticallyCropImagesToAspectRatio('3:1')
                            ->automaticallyResizeImagesToWidth('900')
                            ->automaticallyResizeImagesToHeight('300'),
                    ]),

                Section::make('Información general')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->openable()
                            ->disk("r2")
                            ->visibility("private")
                            ->directory('restaurants/logos')
                            ->imageEditor()
                            ->imageAspectRatio('1:1')
                            ->automaticallyOpenImageEditorForAspectRatio()
                            ->automaticallyCropImagesToAspectRatio('1:1')
                            ->automaticallyResizeImagesToWidth('300')
                            ->automaticallyResizeImagesToHeight('300'),

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
                            ->openable()
                            ->disk("r2")
                            ->visibility("private")
                            ->directory('restaurants/references')
                            ->imageEditor()
                            ->imageAspectRatio('16:9')
                            ->automaticallyOpenImageEditorForAspectRatio()
                            ->automaticallyCropImagesToAspectRatio('16:9')
                            ->automaticallyResizeImagesToWidth('1280')
                            ->automaticallyResizeImagesToHeight('720'),
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
