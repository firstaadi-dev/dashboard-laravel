<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static UnitEnum|string|null $navigationGroup = 'Inventory';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Products'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canViewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_categories') || $user->hasRole('super_admin');
    }

    public static function canCreate(User $user): bool
    {
        return $user->hasPermissionTo('create_categories') || $user->hasRole('super_admin');
    }

    public static function canEdit(User $user, $record): bool
    {
        return $user->hasPermissionTo('edit_categories') || $user->hasRole('super_admin');
    }

    public static function canDelete(User $user, $record): bool
    {
        return $user->hasPermissionTo('delete_categories') || $user->hasRole('super_admin');
    }

    public static function canDeleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_categories') || $user->hasRole('super_admin');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategories::route('/'),
        ];
    }
}
