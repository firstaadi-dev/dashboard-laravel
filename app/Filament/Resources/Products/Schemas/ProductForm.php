<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Information')
                    ->schema([
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        TextInput::make('SKU')
                            ->label('SKU')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->alphaDash(),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Section::make('Inventory & Pricing')
                    ->schema([
                        TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix('units'),

                        TextInput::make('unit_name')
                            ->label('Unit')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., pcs, kg, liter'),

                        TextInput::make('price')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(",")
                            ->required()
                            ->prefix('Rp.')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->inputMode('decimal'),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}
