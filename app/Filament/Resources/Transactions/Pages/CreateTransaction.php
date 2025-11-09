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
        if ($transaction->total_amount != $totalAmount) {
            $transaction->update(['total_amount' => $totalAmount]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
