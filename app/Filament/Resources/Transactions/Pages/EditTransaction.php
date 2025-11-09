<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function afterSave(): void
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
}
