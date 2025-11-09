<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Auth;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transaksi')
                    ->schema([
                        Hidden::make('user_id')
                            ->default(fn () => Auth::id())
                            ->required(),

                        TextInput::make('transaction_number')
                            ->label('Nomor Transaksi')
                            ->default(fn () => 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),

                        DateTimePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->default(now())
                            ->required()
                            ->native(false),

                        TextInput::make('customer_name')
                            ->label('Nama Pelanggan')
                            ->required()
                            ->maxLength(255),

                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cash' => 'Tunai',
                                'transfer' => 'Transfer Bank',
                                'debit' => 'Kartu Debit',
                                'credit' => 'Kartu Kredit',
                                'e-wallet' => 'E-Wallet',
                            ])
                            ->required()
                            ->native(false),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Item Transaksi')
                    ->schema([
                        Repeater::make('items')
                            ->label('')
                            ->relationship('items')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Produk')
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if ($state) {
                                            $product = \App\Models\Product::find($state);
                                            if ($product) {
                                                $set('unit_price', $product->price);
                                                $quantity = $get('quantity') ?? 1;
                                                $set('subtotal', $product->price * $quantity);
                                            }
                                        }
                                    }),

                                TextInput::make('quantity')
                                    ->label('Jumlah')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0.01)
                                    ->step(0.01)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $unitPrice = $get('unit_price') ?? 0;
                                        $set('subtotal', $state * $unitPrice);
                                    }),

                                TextInput::make('unit_price')
                                    ->label('Harga Satuan')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(",")
                                    ->required()
                                    ->prefix('Rp.')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $quantity = $get('quantity') ?? 1;
                                        $set('subtotal', $state * $quantity);
                                    }),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(",")
                                    ->required()
                                    ->prefix('Rp.')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->addActionLabel('Tambah Item')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                $state['product_id']
                                    ? \App\Models\Product::find($state['product_id'])?->name
                                    : null
                            ),
                    ])
                    ->columnSpanFull(),

                Section::make('Total')
                    ->schema([
                        TextInput::make('total_amount')
                            ->label('Total Pembayaran')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(",")
                            ->required()
                            ->prefix('Rp.')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->default(0),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
