<?php

namespace App\Filament\Resources\PurchaseOrders\Tables;

use App\Exports\PurchaseOrdersExport;
use App\Filament\Tables\Columns;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('po_number')
                    ->label('Nomor PO')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor PO berhasil disalin!')
                    ->weight('bold'),

                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->sortable()
                    ->searchable()
                    ->limit(30),

                TextColumn::make('order_date')
                    ->label('Tanggal Order')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('expected_delivery_date')
                    ->label('Tanggal Diharapkan')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('items_count')
                    ->label('Jumlah Item')
                    ->counts('items')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'received' => 'Received',
                        'cancelled' => 'Cancelled',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'info',
                        'approved' => 'warning',
                        'received' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-o-document',
                        'submitted' => 'heroicon-o-paper-airplane',
                        'approved' => 'heroicon-o-check-badge',
                        'received' => 'heroicon-o-check-circle',
                        'cancelled' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable(),

                ...Columns::timestamps(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'received' => 'Received',
                        'cancelled' => 'Cancelled',
                    ])
                    ->native(false),

                SelectFilter::make('supplier')
                    ->label('Supplier')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                Filter::make('order_date')
                    ->form([
                        DatePicker::make('order_date_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('order_date_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['order_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('order_date', '>=', $date),
                            )
                            ->when(
                                $data['order_date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('order_date', '<=', $date),
                            );
                    }),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => Excel::download(new PurchaseOrdersExport(), 'purchase-orders-' . date('Y-m-d') . '.xlsx'))
                    ->color('success'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
