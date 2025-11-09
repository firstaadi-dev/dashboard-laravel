<?php

namespace App\Filament\Resources\StockMovements;

use App\Exports\StockMovementsExport;
use App\Filament\Resources\StockMovements\Pages\ManageStockMovements;
use App\Models\Product;
use App\Models\StockMovement;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-arrow-path';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('view_stock_movements') || $user->hasRole('super_admin'));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('create_stock_movements') || $user->hasRole('super_admin'));
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('edit_stock_movements') || $user->hasRole('super_admin'));
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_stock_movements') || $user->hasRole('super_admin'));
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_stock_movements') || $user->hasRole('super_admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        if ($state) {
                            $product = Product::find($state);
                            if ($product) {
                                $set('previous_stock', $product->stock);
                                self::calculateNewStock($get, $set);
                            }
                        }
                    }),

                Hidden::make('user_id')
                    ->default(auth()->id())
                    ->required(),

                TextInput::make('reference_number')
                    ->label('Reference Number')
                    ->default(fn () => 'SM-' . date('Ymd') . '-' . str_pad(StockMovement::whereDate('created_at', today())->count() + 1, 6, '0', STR_PAD_LEFT))
                    ->required()
                    ->unique(StockMovement::class, 'reference_number', ignoreRecord: true)
                    ->maxLength(255),

                Select::make('type')
                    ->label('Type')
                    ->options([
                        'in' => 'In',
                        'out' => 'Out',
                        'adjustment' => 'Adjustment',
                        'transfer' => 'Transfer',
                    ])
                    ->required()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::calculateNewStock($get, $set);
                    }),

                TextInput::make('quantity')
                    ->label('Quantity')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::calculateNewStock($get, $set);
                    }),

                TextInput::make('previous_stock')
                    ->label('Previous Stock')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->default(0),

                TextInput::make('new_stock')
                    ->label('New Stock')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->default(0),

                TextInput::make('reference_type')
                    ->label('Reference Type')
                    ->maxLength(255),

                TextInput::make('reference_id')
                    ->label('Reference ID')
                    ->numeric(),

                DateTimePicker::make('movement_date')
                    ->label('Movement Date')
                    ->required()
                    ->default(now())
                    ->native(false),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    protected static function calculateNewStock(Get $get, Set $set): void
    {
        $type = $get('type');
        $quantity = $get('quantity');
        $previousStock = $get('previous_stock');

        if ($type && $quantity !== null && $previousStock !== null) {
            $newStock = match ($type) {
                'in' => $previousStock + $quantity,
                'out' => $previousStock - $quantity,
                'adjustment' => $quantity, // For adjustment, quantity is the new stock value
                'transfer' => $previousStock - $quantity, // Transfer out
                default => $previousStock,
            };

            $set('new_stock', max(0, $newStock));
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')
                    ->label('Reference Number')
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        'adjustment' => 'warning',
                        'transfer' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->formatStateUsing(function ($record) {
                        $sign = match ($record->type) {
                            'in' => '+',
                            'out' => '-',
                            'transfer' => '-',
                            default => '',
                        };
                        return $sign . number_format($record->quantity, 2);
                    })
                    ->sortable(),

                TextColumn::make('stock_movement')
                    ->label('Stock Movement')
                    ->formatStateUsing(function ($record) {
                        return number_format($record->previous_stock, 2) . ' → ' . number_format($record->new_stock, 2);
                    }),

                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),

                TextColumn::make('movement_date')
                    ->label('Movement Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'in' => 'In',
                        'out' => 'Out',
                        'adjustment' => 'Adjustment',
                        'transfer' => 'Transfer',
                    ]),

                SelectFilter::make('product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('date_range')
                    ->form([
                        DateTimePicker::make('movement_date_from')
                            ->label('From Date'),
                        DateTimePicker::make('movement_date_until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['movement_date_from'],
                                fn (Builder $query, $date): Builder => $query->where('movement_date', '>=', $date),
                            )
                            ->when(
                                $data['movement_date_until'],
                                fn (Builder $query, $date): Builder => $query->where('movement_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => Excel::download(new StockMovementsExport(), 'stock-movements-' . date('Y-m-d') . '.xlsx'))
                    ->color('success'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('movement_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStockMovements::route('/'),
        ];
    }
}
