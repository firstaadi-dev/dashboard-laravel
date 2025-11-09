<?php

namespace App\Filament\Resources\Products;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\ViewProduct;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Schemas\ProductInfolist;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cube';

    protected static UnitEnum|string|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('view_products') || $user->hasRole('super_admin'));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('create_products') || $user->hasRole('super_admin'));
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('edit_products') || $user->hasRole('super_admin'));
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_products') || $user->hasRole('super_admin'));
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_products') || $user->hasRole('super_admin'));
    }

    public static function canForceDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_products') || $user->hasRole('super_admin'));
    }

    public static function canForceDeleteAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_products') || $user->hasRole('super_admin'));
    }

    public static function canRestore($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_products') || $user->hasRole('super_admin'));
    }

    public static function canRestoreAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('delete_products') || $user->hasRole('super_admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
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
