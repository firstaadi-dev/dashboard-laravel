<?php

namespace App\Filament\Resources\JournalEntries\Tables;

use App\Exports\JournalEntriesExport;
use App\Filament\Tables\Columns;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class JournalEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('entry_number')
                    ->label('Nomor Entry')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor entry berhasil disalin!')
                    ->weight('bold'),

                TextColumn::make('entry_date')
                    ->label('Tanggal Entry')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('lines_count')
                    ->label('Jumlah Baris')
                    ->counts('lines')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('total_debit')
                    ->label('Total Debit')
                    ->money('IDR')
                    ->getStateUsing(function ($record) {
                        return $record->lines->sum('debit');
                    })
                    ->weight('medium')
                    ->color('info'),

                TextColumn::make('total_credit')
                    ->label('Total Credit')
                    ->money('IDR')
                    ->getStateUsing(function ($record) {
                        return $record->lines->sum('credit');
                    })
                    ->weight('medium')
                    ->color('warning'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'posted' => 'Posted',
                        'reversed' => 'Reversed',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'posted' => 'success',
                        'reversed' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-o-document',
                        'posted' => 'heroicon-o-check-circle',
                        'reversed' => 'heroicon-o-arrow-uturn-left',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable(),

                TextColumn::make('posted_at')
                    ->label('Posted At')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ...Columns::timestamps(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'posted' => 'Posted',
                        'reversed' => 'Reversed',
                    ])
                    ->native(false),

                Filter::make('entry_date')
                    ->form([
                        DatePicker::make('entry_date_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('entry_date_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['entry_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('entry_date', '>=', $date),
                            )
                            ->when(
                                $data['entry_date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('entry_date', '<=', $date),
                            );
                    }),

                SelectFilter::make('account')
                    ->label('Akun')
                    ->relationship('lines.account', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => Excel::download(new JournalEntriesExport(), 'journal-entries-' . date('Y-m-d') . '.xlsx'))
                    ->color('success'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            // Only delete draft entries
                            foreach ($records as $record) {
                                if ($record->status === 'draft') {
                                    $record->delete();
                                }
                            }
                        })
                        ->successNotificationTitle('Draft journal entries deleted'),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            // Only force delete draft entries
                            foreach ($records as $record) {
                                if ($record->status === 'draft') {
                                    $record->forceDelete();
                                }
                            }
                        })
                        ->successNotificationTitle('Draft journal entries permanently deleted'),
                ]),
            ]);
    }
}
