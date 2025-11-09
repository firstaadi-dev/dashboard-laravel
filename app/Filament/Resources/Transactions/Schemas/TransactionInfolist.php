<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transaksi')
                    ->schema([
                        TextEntry::make('transaction_number')
                            ->label('Nomor Transaksi')
                            ->copyable()
                            ->copyMessage('Nomor transaksi berhasil disalin!'),

                        TextEntry::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->dateTime('d M Y, H:i'),

                        TextEntry::make('customer_name')
                            ->label('Nama Pelanggan'),

                        TextEntry::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'cash' => 'Tunai',
                                'transfer' => 'Transfer Bank',
                                'debit' => 'Kartu Debit',
                                'credit' => 'Kartu Kredit',
                                'e-wallet' => 'E-Wallet',
                                default => $state,
                            }),

                        TextEntry::make('status')
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
                            }),

                        TextEntry::make('user.name')
                            ->label('Dibuat Oleh'),

                        TextEntry::make('notes')
                            ->label('Catatan')
                            ->default('-'),
                    ])
                    ->columns(2),

                Section::make('Item Transaksi')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Produk'),

                                TextEntry::make('quantity')
                                    ->label('Jumlah')
                                    ->numeric(decimalPlaces: 2),

                                TextEntry::make('unit_price')
                                    ->label('Harga Satuan')
                                    ->money('IDR'),

                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->money('IDR')
                                    ->weight('bold'),
                            ])
                            ->columns(4),
                    ]),

                Section::make('Total')
                    ->schema([
                        TextEntry::make('total_amount')
                            ->label('Total Pembayaran')
                            ->money('IDR')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success'),
                    ]),
            ]);
    }
}
