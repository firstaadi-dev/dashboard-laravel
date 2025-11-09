<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('log_name')
                    ->label('Log Name')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('subject_type')
                    ->label('Subject Type')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->badge(),

                TextColumn::make('subject_id')
                    ->label('Subject ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('causer.name')
                    ->label('User')
                    ->sortable()
                    ->searchable()
                    ->default('System')
                    ->weight('medium'),

                TextColumn::make('properties')
                    ->label('Properties')
                    ->limit(50)
                    ->tooltip(fn ($record): string => json_encode($record->properties, JSON_PRETTY_PRINT))
                    ->formatStateUsing(fn ($state): string => json_encode($state)),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('subject_type')
                    ->label('Model Type')
                    ->options([
                        'App\Models\Customer' => 'Customer',
                        'App\Models\Supplier' => 'Supplier',
                        'App\Models\Product' => 'Product',
                        'App\Models\Category' => 'Category',
                        'App\Models\Transaction' => 'Transaction',
                        'App\Models\PurchaseOrder' => 'Purchase Order',
                        'App\Models\PurchaseOrderItem' => 'Purchase Order Item',
                        'App\Models\StockMovement' => 'Stock Movement',
                        'App\Models\Employee' => 'Employee',
                        'App\Models\Attendance' => 'Attendance',
                        'App\Models\Account' => 'Account',
                        'App\Models\JournalEntry' => 'Journal Entry',
                        'App\Models\JournalEntryLine' => 'Journal Entry Line',
                    ])
                    ->searchable(),

                SelectFilter::make('description')
                    ->label('Action')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),

                SelectFilter::make('causer_id')
                    ->label('User')
                    ->relationship('causer', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('From Date'),
                        DatePicker::make('created_until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
