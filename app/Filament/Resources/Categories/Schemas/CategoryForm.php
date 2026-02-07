<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;

use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Información')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('icon_path')
                            ->label('Icono')
                            ->image()
                            ->directory('categories/icons')
                            ->imagePreviewHeight(200),

                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(20),

                                Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }
}
