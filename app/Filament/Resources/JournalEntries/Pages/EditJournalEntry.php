<?php

namespace App\Filament\Resources\JournalEntries\Pages;

use App\Filament\Resources\JournalEntries\JournalEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJournalEntry extends EditRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load lines to calculate balance check fields
        $journalEntry = $this->record;
        $journalEntry->load('lines');

        $totalDebit = $journalEntry->lines->sum('debit');
        $totalCredit = $journalEntry->lines->sum('credit');

        // Add calculated balance check fields to form data
        $data['total_debit'] = $totalDebit;
        $data['total_credit'] = $totalCredit;
        $data['balance'] = $totalDebit - $totalCredit;

        return $data;
    }
}
