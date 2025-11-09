<?php

namespace App\Filament\Resources\Suppliers;

use App\Filament\Resources\Suppliers\Pages\ManageSuppliers;
use BackedEnum;
use UnitEnum;
use App\Filament\Tables\Columns;
use App\Models\Supplier;
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

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office';

    protected static UnitEnum|string|null $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('view_suppliers') || $user->hasRole('super_admin'));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('create_suppliers') || $user->hasRole('super_admin'));
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('edit_suppliers') || $user->hasRole('super_admin'));
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_suppliers') || $user->hasRole('super_admin'));
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_suppliers') || $user->hasRole('super_admin'));
    }

    public static function canForceDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_suppliers') || $user->hasRole('super_admin'));
    }

    public static function canForceDeleteAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_suppliers') || $user->hasRole('super_admin'));
    }

    public static function canRestore($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_suppliers') || $user->hasRole('super_admin'));
    }

    public static function canRestoreAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_suppliers') || $user->hasRole('super_admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->alphaDash()
                            ->placeholder('e.g., SUP-001'),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('contact_person')
                            ->label('Contact Person')
                            ->maxLength(255)
                            ->placeholder('Name of primary contact'),

                        Select::make('payment_terms')
                            ->label('Payment Terms')
                            ->options([
                                'cod' => 'Cash on Delivery (COD)',
                                'net_7' => 'Net 7 Days',
                                'net_15' => 'Net 15 Days',
                                'net_30' => 'Net 30 Days',
                                'net_45' => 'Net 45 Days',
                                'net_60' => 'Net 60 Days',
                            ])
                            ->required()
                            ->default('net_30')
                            ->native(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),

                        Textarea::make('address')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('city')
                            ->maxLength(255),

                        TextInput::make('province')
                            ->maxLength(255),

                        TextInput::make('postal_code')
                            ->maxLength(255)
                            ->placeholder('e.g., 12345'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Tax & Financial Information')
                    ->schema([
                        TextInput::make('tax_id')
                            ->label('Tax ID (NPWP)')
                            ->maxLength(255)
                            ->placeholder('e.g., 12.345.678.9-012.345'),

                        TextInput::make('credit_balance')
                            ->label('Credit Balance')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->prefix('Rp.')
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->inputMode('decimal')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Updated automatically based on purchase orders'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Active Status')
                            ->default(true)
                            ->inline(false),
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
                    ->label('Supplier Code')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Code copied!')
                    ->weight('bold'),

                TextColumn::make('name')
                    ->label('Supplier Name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('contact_person')
                    ->label('Contact Person')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('city')
                    ->label('City')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('payment_terms')
                    ->label('Payment Terms')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cod' => 'success',
                        'net_7' => 'info',
                        'net_15' => 'info',
                        'net_30' => 'warning',
                        'net_45' => 'warning',
                        'net_60' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cod' => 'COD',
                        'net_7' => 'Net 7',
                        'net_15' => 'Net 15',
                        'net_30' => 'Net 30',
                        'net_45' => 'Net 45',
                        'net_60' => 'Net 60',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                TextColumn::make('credit_balance')
                    ->label('Credit Balance')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                ...Columns::timestamps(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('payment_terms')
                    ->label('Payment Terms')
                    ->options([
                        'cod' => 'COD',
                        'net_7' => 'Net 7',
                        'net_15' => 'Net 15',
                        'net_30' => 'Net 30',
                        'net_45' => 'Net 45',
                        'net_60' => 'Net 60',
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
            'index' => ManageSuppliers::route('/'),
        ];
    }
}
