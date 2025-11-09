<?php

namespace App\Filament\Resources\PurchaseOrders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Purchase Order')
                    ->schema([
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->relationship('supplier', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->helperText('Pilih supplier untuk purchase order ini'),

                        Hidden::make('user_id')
                            ->default(fn () => Auth::id())
                            ->required(),

                        TextInput::make('po_number')
                            ->label('Nomor PO')
                            ->default(fn () => 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),

                        DatePicker::make('order_date')
                            ->label('Tanggal Order')
                            ->default(now())
                            ->required()
                            ->native(false),

                        DatePicker::make('expected_delivery_date')
                            ->label('Tanggal Pengiriman Diharapkan')
                            ->native(false)
                            ->helperText('Optional: Kapan barang diharapkan tiba'),

                        DatePicker::make('received_date')
                            ->label('Tanggal Diterima')
                            ->native(false)
                            ->visible(fn (callable $get) => $get('status') === 'received')
                            ->helperText('Tanggal barang diterima'),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'submitted' => 'Submitted',
                                'approved' => 'Approved',
                                'received' => 'Received',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required()
                            ->native(false)
                            ->live(),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Item Purchase Order')
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
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if ($state) {
                                            $product = \App\Models\Product::find($state);
                                            if ($product) {
                                                $set('unit_price', $product->price);
                                                self::calculateItemSubtotal($get, $set);
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
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (callable $get, callable $set) {
                                        self::calculateItemSubtotal($get, $set);
                                    }),

                                TextInput::make('unit_price')
                                    ->label('Harga Satuan')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(",")
                                    ->required()
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (callable $get, callable $set) {
                                        self::calculateItemSubtotal($get, $set);
                                    }),

                                TextInput::make('discount_percentage')
                                    ->label('Diskon (%)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(0.01)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (callable $get, callable $set) {
                                        self::calculateItemSubtotal($get, $set);
                                    }),

                                TextInput::make('tax_percentage')
                                    ->label('Pajak (%)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(0.01)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (callable $get, callable $set) {
                                        self::calculateItemSubtotal($get, $set);
                                    }),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(",")
                                    ->required()
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->live(),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->addActionLabel('Tambah Item')
                            ->reorderable(false)
                            ->collapsible()
                            ->live()
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                self::updateTotals($get, $set);
                            })
                            ->deleteAction(
                                fn ($action) => $action->after(fn (callable $get, callable $set) => self::updateTotals($get, $set))
                            )
                            ->itemLabel(fn (array $state): ?string =>
                                $state['product_id']
                                    ? \App\Models\Product::find($state['product_id'])?->name
                                    : null
                            ),
                    ])
                    ->columnSpanFull(),

                Section::make('Total')
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal (sebelum diskon & pajak)')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(",")
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->default(0)
                            ->live()
                            ->helperText('Total dari semua item sebelum diskon dan pajak'),

                        TextInput::make('tax_amount')
                            ->label('Pajak Tambahan')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(",")
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                self::calculateTotal($get, $set);
                            }),

                        TextInput::make('discount_amount')
                            ->label('Diskon Tambahan')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(",")
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                self::calculateTotal($get, $set);
                            }),

                        TextInput::make('shipping_cost')
                            ->label('Biaya Pengiriman')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(",")
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                self::calculateTotal($get, $set);
                            }),

                        TextInput::make('total_amount')
                            ->label('Total Pembayaran')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(",")
                            ->required()
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->default(0)
                            ->live()
                            ->extraAttributes(['class' => 'font-bold text-lg']),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Calculate item subtotal: quantity * unit_price * (1 - discount_percentage/100) * (1 + tax_percentage/100)
     */
    protected static function calculateItemSubtotal(callable $get, callable $set): void
    {
        $quantity = floatval($get('quantity') ?? 0);
        $unitPrice = floatval($get('unit_price') ?? 0);
        $discountPercentage = floatval($get('discount_percentage') ?? 0);
        $taxPercentage = floatval($get('tax_percentage') ?? 0);

        $subtotal = $quantity * $unitPrice;
        $subtotal = $subtotal * (1 - ($discountPercentage / 100));
        $subtotal = $subtotal * (1 + ($taxPercentage / 100));

        $set('subtotal', $subtotal);

        // Update totals at the parent level
        self::updateTotalsFromItem($get, $set);
    }

    /**
     * Update totals from within an item (using ../ path)
     */
    protected static function updateTotalsFromItem(callable $get, callable $set): void
    {
        $items = $get('../../items');

        if (!is_array($items)) {
            return;
        }

        $subtotal = 0;
        foreach ($items as $item) {
            $itemSubtotal = floatval($item['subtotal'] ?? 0);
            $subtotal += $itemSubtotal;
        }

        $set('../../subtotal', $subtotal);

        // Recalculate total
        $taxAmount = floatval($get('../../tax_amount') ?? 0);
        $discountAmount = floatval($get('../../discount_amount') ?? 0);
        $shippingCost = floatval($get('../../shipping_cost') ?? 0);

        $total = $subtotal + $taxAmount - $discountAmount + $shippingCost;
        $set('../../total_amount', $total);
    }

    /**
     * Update totals (called from repeater level)
     */
    protected static function updateTotals(callable $get, callable $set): void
    {
        $items = $get('items');

        if (!is_array($items)) {
            $set('subtotal', 0);
            $set('total_amount', 0);
            return;
        }

        $subtotal = 0;
        foreach ($items as $item) {
            $itemSubtotal = floatval($item['subtotal'] ?? 0);
            $subtotal += $itemSubtotal;
        }

        $set('subtotal', $subtotal);

        // Recalculate total
        $taxAmount = floatval($get('tax_amount') ?? 0);
        $discountAmount = floatval($get('discount_amount') ?? 0);
        $shippingCost = floatval($get('shipping_cost') ?? 0);

        $total = $subtotal + $taxAmount - $discountAmount + $shippingCost;
        $set('total_amount', $total);
    }

    /**
     * Calculate total amount: subtotal + tax_amount - discount_amount + shipping_cost
     */
    protected static function calculateTotal(callable $get, callable $set): void
    {
        $subtotal = floatval($get('subtotal') ?? 0);
        $taxAmount = floatval($get('tax_amount') ?? 0);
        $discountAmount = floatval($get('discount_amount') ?? 0);
        $shippingCost = floatval($get('shipping_cost') ?? 0);

        $total = $subtotal + $taxAmount - $discountAmount + $shippingCost;
        $set('total_amount', $total);
    }
}
