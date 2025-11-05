<?php

namespace App\Filament\Tables;

use Filament\Tables\Columns\TextColumn;

class Columns
{
    public static function category(?string $columnName = null): TextColumn
    {
        return TextColumn::make($columnName ?? 'category.name')
            ->label('Category')
            ->sortable()
            ->searchable()
            ->badge()
            ->color('info');
    }

    public static function categoryJoined(): TextColumn
    {
        return self::category('category_name');
    }

    public static function price(): TextColumn
    {
        return TextColumn::make('price')
            ->label('Price')
            ->sortable()
            ->money('IDR')
            ->color('success');
    }

    public static function timestamps(): array
    {
        return [
            TextColumn::make('created_at')
                ->label('Created')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('updated_at')
                ->label('Updated')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
