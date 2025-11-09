<?php

namespace App\Filament\Resources\JournalEntries;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\JournalEntries\Pages\CreateJournalEntry;
use App\Filament\Resources\JournalEntries\Pages\EditJournalEntry;
use App\Filament\Resources\JournalEntries\Pages\ListJournalEntries;
use App\Filament\Resources\JournalEntries\Schemas\JournalEntryForm;
use App\Filament\Resources\JournalEntries\Tables\JournalEntriesTable;
use App\Models\JournalEntry;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-book-open';

    protected static UnitEnum|string|null $navigationGroup = 'Accounting';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'entry_number';

    protected static ?string $navigationLabel = 'Journal Entries';

    protected static ?string $pluralModelLabel = 'Journal Entries';

    public static function canViewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_journal_entries') || $user->hasRole('super_admin');
    }

    public static function canCreate(User $user): bool
    {
        return $user->hasPermissionTo('create_journal_entries') || $user->hasRole('super_admin');
    }

    public static function canEdit(User $user, $record): bool
    {
        return $user->hasPermissionTo('edit_journal_entries') || $user->hasRole('super_admin');
    }

    public static function canDelete(User $user, $record): bool
    {
        return $user->hasPermissionTo('delete_journal_entries') || $user->hasRole('super_admin');
    }

    public static function canDeleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_journal_entries') || $user->hasRole('super_admin');
    }

    public static function canForceDelete(User $user, $record): bool
    {
        return $user->hasPermissionTo('delete_journal_entries') || $user->hasRole('super_admin');
    }

    public static function canForceDeleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_journal_entries') || $user->hasRole('super_admin');
    }

    public static function canRestore(User $user, $record): bool
    {
        return $user->hasPermissionTo('delete_journal_entries') || $user->hasRole('super_admin');
    }

    public static function canRestoreAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_journal_entries') || $user->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return JournalEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JournalEntriesTable::configure($table);
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
            'index' => ListJournalEntries::route('/'),
            'create' => CreateJournalEntry::route('/create'),
            'edit' => EditJournalEntry::route('/{record}/edit'),
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
