<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Tables\Columns;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('SKU')
                    ->label('SKU')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('SKU copied!'),

                Columns::categoryJoined(),

                TextColumn::make('name')
                    ->label('Product Name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable()
                    ->numeric()
                    ->suffix(fn ($record) => ' ' . $record->unit_name)
                    ->color(fn ($state) => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),

                Columns::price(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return match (true) {
                            $record->stock <= 0 => 'Out of Stock',
                            $record->stock <= 20 => 'Low Stock',
                            $record->stock <= 50 => 'In Stock',
                            default => 'Well Stocked',
                        };
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Out of Stock' => 'danger',
                        'Low Stock' => 'warning',
                        'In Stock' => 'success',
                        'Well Stocked' => 'info',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Out of Stock' => 'heroicon-o-x-circle',
                        'Low Stock' => 'heroicon-o-exclamation-triangle',
                        'In Stock' => 'heroicon-o-check-circle',
                        'Well Stocked' => 'heroicon-o-check-badge',
                    }),

                ...Columns::timestamps(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('edit_stock')
                    ->label('Edit Stock')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        TextInput::make('stock')
                            ->label('Stock')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix(fn ($record) => $record->unit_name)
                            ->autofocus(),
                    ])
                    ->fillForm(fn ($record) => [
                        'stock' => $record->stock,
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'stock' => $data['stock'],
                        ]);
                    })
                    ->successNotificationTitle('Stock updated successfully'),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
