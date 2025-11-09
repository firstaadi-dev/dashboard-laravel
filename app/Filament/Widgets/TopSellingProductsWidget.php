<?php

namespace App\Filament\Widgets;

use App\Models\TransactionItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopSellingProductsWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TransactionItem::query()
                    ->select([
                        'product_id',
                        DB::raw('SUM(quantity) as total_quantity'),
                        DB::raw('COUNT(*) as times_sold'),
                        DB::raw('SUM(subtotal) as total_revenue'),
                    ])
                    ->whereHas('transaction', function ($query) {
                        $query->where('status', 'completed')
                            ->where('transaction_date', '>=', now()->subDays(30));
                    })
                    ->groupBy('product_id')
                    ->orderBy('total_quantity', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.SKU')
                    ->label('SKU')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record): string => route('filament.admin.resources.products.edit', ['record' => $record->product_id]))
                    ->color('primary'),
                Tables\Columns\TextColumn::make('product.category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Total Sold')
                    ->sortable()
                    ->formatStateUsing(fn ($record): string => number_format($record->total_quantity, 2) . ' ' . $record->product->unit_name),
                Tables\Columns\TextColumn::make('times_sold')
                    ->label('Times Sold')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => number_format($state) . ' transactions'),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Total Revenue')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.stock')
                    ->label('Current Stock')
                    ->sortable()
                    ->formatStateUsing(fn ($record): string => $record->product->stock . ' ' . $record->product->unit_name)
                    ->color(fn ($record): string => $record->product->stock <= 10 ? 'danger' : 'success'),
            ])
            ->heading('Top Selling Products (Last 30 Days)')
            ->defaultSort('total_quantity', 'desc')
            ->emptyStateHeading('No sales data available')
            ->emptyStateDescription('No completed transactions found in the last 30 days.')
            ->emptyStateIcon('heroicon-o-chart-bar');
    }

    public static function canView(): bool
    {
        return Auth::user()?->can('view_transactions') ?? false;
    }
}
