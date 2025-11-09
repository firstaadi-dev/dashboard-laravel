<?php

namespace App\Filament\Resources\PurchaseOrders;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\PurchaseOrders\Pages\CreatePurchaseOrder;
use App\Filament\Resources\PurchaseOrders\Pages\EditPurchaseOrder;
use App\Filament\Resources\PurchaseOrders\Pages\ListPurchaseOrders;
use App\Filament\Resources\PurchaseOrders\Schemas\PurchaseOrderForm;
use App\Filament\Resources\PurchaseOrders\Tables\PurchaseOrdersTable;
use App\Models\PurchaseOrder;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static UnitEnum|string|null $navigationGroup = 'Purchasing';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'po_number';

    protected static ?string $navigationLabel = 'Purchase Orders';

    protected static ?string $pluralModelLabel = 'Purchase Orders';

    public static function canViewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_purchase_orders') || $user->hasRole('super_admin');
    }

    public static function canCreate(User $user): bool
    {
        return $user->hasPermissionTo('create_purchase_orders') || $user->hasRole('super_admin');
    }

    public static function canEdit(User $user, $record): bool
    {
        return $user->hasPermissionTo('edit_purchase_orders') || $user->hasRole('super_admin');
    }

    public static function canDelete(User $user, $record): bool
    {
        return $user->hasPermissionTo('delete_purchase_orders') || $user->hasRole('super_admin');
    }

    public static function canDeleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_purchase_orders') || $user->hasRole('super_admin');
    }

    public static function canForceDelete(User $user, $record): bool
    {
        return $user->hasPermissionTo('delete_purchase_orders') || $user->hasRole('super_admin');
    }

    public static function canForceDeleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_purchase_orders') || $user->hasRole('super_admin');
    }

    public static function canRestore(User $user, $record): bool
    {
        return $user->hasPermissionTo('delete_purchase_orders') || $user->hasRole('super_admin');
    }

    public static function canRestoreAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_purchase_orders') || $user->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return PurchaseOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchaseOrdersTable::configure($table);
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
            'index' => ListPurchaseOrders::route('/'),
            'create' => CreatePurchaseOrder::route('/create'),
            'edit' => EditPurchaseOrder::route('/{record}/edit'),
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
