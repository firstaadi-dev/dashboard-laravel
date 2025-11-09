<?php

namespace App\Filament\Resources\Transactions;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Resources\Transactions\Pages\ViewTransaction;
use App\Filament\Resources\Transactions\Schemas\TransactionForm;
use App\Filament\Resources\Transactions\Schemas\TransactionInfolist;
use App\Filament\Resources\Transactions\Tables\TransactionsTable;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static UnitEnum|string|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'transaction_number';

    protected static ?string $navigationLabel = 'Transaksi';

    protected static ?string $pluralModelLabel = 'Transaksi';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('view_transactions') || $user->hasRole('super_admin'));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('create_transactions') || $user->hasRole('super_admin'));
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('edit_transactions') || $user->hasRole('super_admin'));
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_transactions') || $user->hasRole('super_admin'));
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_transactions') || $user->hasRole('super_admin'));
    }

    public static function canForceDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_transactions') || $user->hasRole('super_admin'));
    }

    public static function canForceDeleteAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_transactions') || $user->hasRole('super_admin'));
    }

    public static function canRestore($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_transactions') || $user->hasRole('super_admin'));
    }

    public static function canRestoreAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_transactions') || $user->hasRole('super_admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return TransactionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'view' => ViewTransaction::route('/{record}'),
            'edit' => EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
