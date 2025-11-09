<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function afterCreate(): void
    {
        // Create journal entry if transaction status is 'completed'
        // This is done here (after items are saved) instead of in the observer's created event
        // to ensure items are already saved when the journal is created
        if ($this->record->status === 'completed') {
            $this->createJournalEntryForTransaction($this->record);
        }
    }

    /**
     * Create journal entry for completed transaction.
     * This method calls the observer's protected method via reflection
     * or we can duplicate the logic here.
     */
    protected function createJournalEntryForTransaction(\App\Models\Transaction $transaction): void
    {
        // Get the observer instance and call its createJournalEntry method
        $observer = app(\App\Observers\TransactionObserver::class);

        // Use reflection to call the protected method
        $reflection = new \ReflectionClass($observer);
        $method = $reflection->getMethod('createJournalEntry');
        $method->setAccessible(true);
        $method->invoke($observer, $transaction);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
