<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Filament\Tables\Columns;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_number')
                    ->label('Nomor Transaksi')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor transaksi berhasil disalin!')
                    ->weight('bold'),

                TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('customer_name')
                    ->label('Pelanggan')
                    ->sortable()
                    ->searchable()
                    ->limit(30),

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

                TextColumn::make('payment_method')
                    ->label('Pembayaran')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer',
                        'debit' => 'Debit',
                        'credit' => 'Kredit',
                        'e-wallet' => 'E-Wallet',
                        default => $state,
                    })
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'completed' => 'heroicon-o-check-circle',
                        'cancelled' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ...Columns::timestamps(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->native(false),

                SelectFilter::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer Bank',
                        'debit' => 'Kartu Debit',
                        'credit' => 'Kartu Kredit',
                        'e-wallet' => 'E-Wallet',
                    ])
                    ->native(false),

                TrashedFilter::make(),
            ])
            ->recordActions([
                //
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
