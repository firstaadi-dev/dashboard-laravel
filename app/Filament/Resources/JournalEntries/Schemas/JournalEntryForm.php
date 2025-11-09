<?php

namespace App\Filament\Resources\JournalEntries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
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
use Illuminate\Support\HtmlString;

class JournalEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Journal Entry')
                    ->schema([
                        Hidden::make('user_id')
                            ->default(fn () => Auth::id())
                            ->required(),

                        TextInput::make('entry_number')
                            ->label('Nomor Entry')
                            ->default(fn () => 'JE-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),

                        DatePicker::make('entry_date')
                            ->label('Tanggal Entry')
                            ->default(now())
                            ->required()
                            ->native(false),

                        TextInput::make('reference_type')
                            ->label('Tipe Referensi')
                            ->maxLength(255)
                            ->helperText('Optional: Tipe model (Transaction, PurchaseOrder, dll)'),

                        TextInput::make('reference_id')
                            ->label('ID Referensi')
                            ->numeric()
                            ->helperText('Optional: ID dari model yang direferensikan'),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->maxLength(1000),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'posted' => 'Posted',
                                'reversed' => 'Reversed',
                            ])
                            ->default('draft')
                            ->required()
                            ->native(false)
                            ->live(),

                        DateTimePicker::make('posted_at')
                            ->label('Posted At')
                            ->native(false)
                            ->disabled()
                            ->dehydrated()
                            ->visible(fn (callable $get) => $get('status') === 'posted')
                            ->helperText('Waktu entry di-post'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Journal Lines')
                    ->schema([
                        Repeater::make('lines')
                            ->label('')
                            ->relationship('lines')
                            ->schema([
                                Select::make('account_id')
                                    ->label('Akun')
                                    ->relationship('account', 'name')
                                    ->required()
                                    ->searchable(['code', 'name'])
                                    ->preload()
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->code} - {$record->name}")
                                    ->columnSpan(2),

                                TextInput::make('description')
                                    ->label('Deskripsi')
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                TextInput::make('debit')
                                    ->label('Debit')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(",")
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        // If debit is entered, clear credit
                                        if ($state > 0) {
                                            $set('credit', 0);
                                        }
                                        self::updateBalances($get, $set);
                                    })
                                    ->rules([
                                        fn (callable $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $debit = floatval($value);
                                            $credit = floatval($get('credit') ?? 0);

                                            // Either debit OR credit must be > 0, not both
                                            if ($debit > 0 && $credit > 0) {
                                                $fail('Debit dan Credit tidak boleh diisi bersamaan.');
                                            }

                                            if ($debit == 0 && $credit == 0) {
                                                $fail('Salah satu dari Debit atau Credit harus lebih besar dari 0.');
                                            }
                                        },
                                    ]),

                                TextInput::make('credit')
                                    ->label('Credit')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(",")
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        // If credit is entered, clear debit
                                        if ($state > 0) {
                                            $set('debit', 0);
                                        }
                                        self::updateBalances($get, $set);
                                    })
                                    ->rules([
                                        fn (callable $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $debit = floatval($get('debit') ?? 0);
                                            $credit = floatval($value);

                                            // Either debit OR credit must be > 0, not both
                                            if ($debit > 0 && $credit > 0) {
                                                $fail('Debit dan Credit tidak boleh diisi bersamaan.');
                                            }

                                            if ($debit == 0 && $credit == 0) {
                                                $fail('Salah satu dari Debit atau Credit harus lebih besar dari 0.');
                                            }
                                        },
                                    ]),
                            ])
                            ->columns(6)
                            ->defaultItems(2)
                            ->addActionLabel('Tambah Baris')
                            ->reorderable(false)
                            ->collapsible()
                            ->live()
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                self::updateBalancesFromRepeater($get, $set);
                            })
                            ->deleteAction(
                                fn ($action) => $action->after(fn (callable $get, callable $set) => self::updateBalancesFromRepeater($get, $set))
                            )
                            ->itemLabel(fn (array $state): ?string =>
                                $state['account_id']
                                    ? \App\Models\Account::find($state['account_id'])?->name
                                    : null
                            ),
                    ])
                    ->columnSpanFull(),

                Section::make('Balance Check')
                    ->schema([
                        TextInput::make('total_debit')
                            ->label('Total Debit')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(",")
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->default(0)
                            ->live()
                            ->extraAttributes(['class' => 'font-bold']),

                        TextInput::make('total_credit')
                            ->label('Total Credit')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(",")
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->default(0)
                            ->live()
                            ->extraAttributes(['class' => 'font-bold']),

                        TextInput::make('balance')
                            ->label('Balance (Debit - Credit)')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(",")
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->default(0)
                            ->live()
                            ->extraAttributes(fn (callable $get) => [
                                'class' => floatval($get('balance') ?? 0) != 0
                                    ? 'font-bold text-red-600 dark:text-red-400'
                                    : 'font-bold text-green-600 dark:text-green-400'
                            ]),

                        Placeholder::make('balance_warning')
                            ->label('')
                            ->content(fn (callable $get) =>
                                floatval($get('balance') ?? 0) != 0
                                    ? new HtmlString('<div class="text-red-600 dark:text-red-400 font-semibold">⚠️ PERINGATAN: Balance harus 0 (Debit harus sama dengan Credit)</div>')
                                    : new HtmlString('<div class="text-green-600 dark:text-green-400 font-semibold">✓ Balance sudah benar (Debit = Credit)</div>')
                            )
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Update balances from within a line item (using ../ path)
     */
    protected static function updateBalances(callable $get, callable $set): void
    {
        $lines = $get('../../lines');

        if (!is_array($lines)) {
            return;
        }

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($lines as $line) {
            $totalDebit += floatval($line['debit'] ?? 0);
            $totalCredit += floatval($line['credit'] ?? 0);
        }

        $set('../../total_debit', $totalDebit);
        $set('../../total_credit', $totalCredit);
        $set('../../balance', $totalDebit - $totalCredit);
    }

    /**
     * Update balances from repeater level
     */
    protected static function updateBalancesFromRepeater(callable $get, callable $set): void
    {
        $lines = $get('lines');

        if (!is_array($lines)) {
            $set('total_debit', 0);
            $set('total_credit', 0);
            $set('balance', 0);
            return;
        }

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($lines as $line) {
            $totalDebit += floatval($line['debit'] ?? 0);
            $totalCredit += floatval($line['credit'] ?? 0);
        }

        $set('total_debit', $totalDebit);
        $set('total_credit', $totalCredit);
        $set('balance', $totalDebit - $totalCredit);
    }
}
