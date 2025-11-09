<?php

namespace App\Filament\Resources\Accounts;

use App\Exports\AccountsExport;
use App\Filament\Resources\Accounts\Pages\ManageAccounts;
use BackedEnum;
use UnitEnum;
use App\Filament\Tables\Columns;
use App\Models\Account;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-calculator';

    protected static UnitEnum|string|null $navigationGroup = 'Accounting';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('view_accounts') || $user->hasRole('super_admin'));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('create_accounts') || $user->hasRole('super_admin'));
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('edit_accounts') || $user->hasRole('super_admin'));
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_accounts') || $user->hasRole('super_admin'));
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_accounts') || $user->hasRole('super_admin'));
    }

    public static function canForceDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_accounts') || $user->hasRole('super_admin'));
    }

    public static function canForceDeleteAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_accounts') || $user->hasRole('super_admin'));
    }

    public static function canRestore($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_accounts') || $user->hasRole('super_admin'));
    }

    public static function canRestoreAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_accounts') || $user->hasRole('super_admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('code')
                            ->label('Account Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->alphaDash()
                            ->placeholder('e.g., 1110 or ACC-001')
                            ->helperText('Standard chart of accounts code'),

                        TextInput::make('name')
                            ->label('Account Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Cash on Hand'),

                        Select::make('parent_id')
                            ->label('Parent Account')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Select parent account for sub-accounts (optional)'),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Section::make('Account Classification')
                    ->schema([
                        Select::make('type')
                            ->label('Account Type')
                            ->options([
                                'asset' => 'Asset',
                                'liability' => 'Liability',
                                'equity' => 'Equity',
                                'revenue' => 'Revenue',
                                'expense' => 'Expense',
                            ])
                            ->required()
                            ->native(false)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('subtype', null)),

                        Select::make('subtype')
                            ->label('Account Subtype')
                            ->options(function (callable $get) {
                                $type = $get('type');
                                return match ($type) {
                                    'asset' => [
                                        'current_asset' => 'Current Asset',
                                        'fixed_asset' => 'Fixed Asset',
                                        'other_asset' => 'Other Asset',
                                    ],
                                    'liability' => [
                                        'current_liability' => 'Current Liability',
                                        'long_term_liability' => 'Long-term Liability',
                                    ],
                                    'equity' => [
                                        'owner_equity' => 'Owner Equity',
                                        'retained_earnings' => 'Retained Earnings',
                                    ],
                                    'revenue' => [
                                        'operating_revenue' => 'Operating Revenue',
                                        'other_revenue' => 'Other Revenue',
                                    ],
                                    'expense' => [
                                        'operating_expense' => 'Operating Expense',
                                        'other_expense' => 'Other Expense',
                                    ],
                                    default => [],
                                };
                            })
                            ->native(false)
                            ->nullable()
                            ->helperText('Select a subtype for detailed classification'),

                        Select::make('normal_balance')
                            ->label('Normal Balance')
                            ->options([
                                'debit' => 'Debit',
                                'credit' => 'Credit',
                            ])
                            ->required()
                            ->native(false)
                            ->default(function (callable $get) {
                                $type = $get('type');
                                return match ($type) {
                                    'asset', 'expense' => 'debit',
                                    'liability', 'equity', 'revenue' => 'credit',
                                    default => 'debit',
                                };
                            })
                            ->helperText('Assets and Expenses normally have debit balance, while Liabilities, Equity, and Revenue have credit balance'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Financial Information')
                    ->schema([
                        TextInput::make('balance')
                            ->label('Current Balance')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->prefix('Rp.')
                            ->default(0)
                            ->step(0.01)
                            ->inputMode('decimal')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Updated automatically from journal entries'),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Brief description of account usage and purpose'),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active Status')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Inactive accounts cannot be used in new transactions'),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Code copied!')
                    ->weight('bold'),

                TextColumn::make('name')
                    ->label('Account Name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('parent.name')
                    ->label('Parent Account')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—'),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'asset' => 'success',
                        'liability' => 'danger',
                        'equity' => 'info',
                        'revenue' => 'warning',
                        'expense' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                TextColumn::make('subtype')
                    ->label('Subtype')
                    ->formatStateUsing(fn (?string $state): string => $state
                        ? str_replace('_', ' ', ucwords($state, '_'))
                        : '—')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('normal_balance')
                    ->label('Normal Balance')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'debit' => 'info',
                        'credit' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('balance')
                    ->label('Balance')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn ($state, $record) => match (true) {
                        $state == 0 => 'gray',
                        ($record->normal_balance === 'debit' && $state > 0) => 'success',
                        ($record->normal_balance === 'credit' && $state > 0) => 'success',
                        default => 'warning',
                    }),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                ...Columns::timestamps(),
            ])
            ->defaultSort('code', 'asc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Account Type')
                    ->options([
                        'asset' => 'Asset',
                        'liability' => 'Liability',
                        'equity' => 'Equity',
                        'revenue' => 'Revenue',
                        'expense' => 'Expense',
                    ])
                    ->native(false),

                SelectFilter::make('subtype')
                    ->label('Account Subtype')
                    ->options([
                        'current_asset' => 'Current Asset',
                        'fixed_asset' => 'Fixed Asset',
                        'other_asset' => 'Other Asset',
                        'current_liability' => 'Current Liability',
                        'long_term_liability' => 'Long-term Liability',
                        'owner_equity' => 'Owner Equity',
                        'retained_earnings' => 'Retained Earnings',
                        'operating_revenue' => 'Operating Revenue',
                        'other_revenue' => 'Other Revenue',
                        'operating_expense' => 'Operating Expense',
                        'other_expense' => 'Other Expense',
                    ])
                    ->native(false),

                SelectFilter::make('normal_balance')
                    ->label('Normal Balance')
                    ->options([
                        'debit' => 'Debit',
                        'credit' => 'Credit',
                    ])
                    ->native(false),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
                    ])
                    ->native(false),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => Excel::download(new AccountsExport(), 'accounts-' . date('Y-m-d') . '.xlsx'))
                    ->color('success'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAccounts::route('/'),
        ];
    }
}
