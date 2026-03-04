<?php

namespace App\Filament\Resources\ProductCategories\Schemas;

use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Forms;

class ProductCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('restaurant_id')
                    ->relationship('restaurant', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->required(),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
