<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LowStockWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('stock', '<=', 10)
                    ->orderBy('stock', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('SKU')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable()
                    ->url(fn (Product $record): string => route('filament.admin.resources.products.edit', ['record' => $record->id]))
                    ->color('primary'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Current Stock')
                    ->sortable()
                    ->formatStateUsing(fn (Product $record): string => $record->stock . ' ' . $record->unit_name),
                Tables\Columns\TextColumn::make('stock_status')
                    ->label('Status')
                    ->badge()
                    ->state(function (Product $record): string {
                        if ($record->stock <= 0) {
                            return 'Out of Stock';
                        } elseif ($record->stock <= 5) {
                            return 'Critical';
                        } else {
                            return 'Low';
                        }
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Out of Stock' => 'danger',
                        'Critical' => 'danger',
                        'Low' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->heading('Low Stock Products')
            ->emptyStateHeading('All products have sufficient stock')
            ->emptyStateDescription('No products are running low on stock.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    public static function canView(): bool
    {
        return Auth::user()?->can('view_products') ?? false;
    }
}
