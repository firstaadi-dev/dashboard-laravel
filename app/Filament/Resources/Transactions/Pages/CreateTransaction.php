<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate total amount from items
        $totalAmount = 0;
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $subtotal = $item['subtotal'] ?? 0;
                // Convert to float to handle string values from form
                $totalAmount += floatval($subtotal);
            }
        }
        $data['total_amount'] = $totalAmount;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Recalculate total amount after items are saved
        $transaction = $this->record;
        $transaction->load('items');

        $totalAmount = $transaction->items->sum('subtotal');

        // Update transaction total if different from calculated
        // Use updateQuietly to prevent triggering observer events again
        if ($transaction->total_amount != $totalAmount) {
            $transaction->updateQuietly(['total_amount' => $totalAmount]);
        }

        // Create journal entry if transaction status is 'completed'
        // This is done here (after items are saved) instead of in the observer's created event
        // to ensure the journal has the correct total amount
        if ($transaction->status === 'completed') {
            $this->createJournalEntryForTransaction($transaction);
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
