<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderObserver
{
    /**
     * Handle the PurchaseOrder "created" event.
     */
    public function created(PurchaseOrder $purchaseOrder): void
    {
        //
    }

    /**
     * Handle the PurchaseOrder "updated" event.
     */
    public function updated(PurchaseOrder $purchaseOrder): void
    {
        // Check if status changed to 'received'
        if ($purchaseOrder->isDirty('status') && $purchaseOrder->status === 'received') {
            $this->createJournalEntry($purchaseOrder);
        }
    }

    /**
     * Handle the PurchaseOrder "deleted" event.
     */
    public function deleted(PurchaseOrder $purchaseOrder): void
    {
        //
    }

    /**
     * Handle the PurchaseOrder "restored" event.
     */
    public function restored(PurchaseOrder $purchaseOrder): void
    {
        //
    }

    /**
     * Handle the PurchaseOrder "force deleted" event.
     */
    public function forceDeleted(PurchaseOrder $purchaseOrder): void
    {
        //
    }

    /**
     * Create journal entry for received purchase order.
     */
    protected function createJournalEntry(PurchaseOrder $purchaseOrder): void
    {
        try {
            // Check if journal entry already exists for this purchase order
            $existingEntry = JournalEntry::where('reference_type', PurchaseOrder::class)
                ->where('reference_id', $purchaseOrder->id)
                ->first();

            if ($existingEntry) {
                return; // Already processed
            }

            DB::beginTransaction();

            // Load supplier relationship
            $purchaseOrder->load('supplier');

            // Determine accounts based on payment terms
            $supplier = $purchaseOrder->supplier;
            $paymentTerms = $supplier?->payment_terms ?? 'cash';

            // Debit account is always Inventory or Purchases
            $debitAccount = $this->findOrCreateAccount('Inventory', 'asset', 'debit');

            // Credit account depends on payment terms
            if (in_array(strtolower($paymentTerms), ['cash', 'cod', 'immediate'])) {
                $creditAccount = $this->findOrCreateAccount('Cash in Hand', 'asset', 'debit');
            } else {
                $creditAccount = $this->findOrCreateAccount('Accounts Payable', 'liability', 'credit');
            }

            // Create journal entry
            $journalEntry = JournalEntry::create([
                'user_id' => $purchaseOrder->user_id,
                'entry_number' => $this->generateEntryNumber(),
                'entry_date' => $purchaseOrder->received_date ?? now(),
                'reference_type' => PurchaseOrder::class,
                'reference_id' => $purchaseOrder->id,
                'description' => "Purchase order {$purchaseOrder->po_number} received from {$supplier?->name}",
                'status' => 'posted',
                'posted_at' => now(),
            ]);

            // Create debit line (Inventory)
            $journalEntry->lines()->create([
                'account_id' => $debitAccount->id,
                'debit' => $purchaseOrder->total_amount,
                'credit' => 0,
                'description' => "Inventory received from {$supplier?->name}",
            ]);

            // Create credit line (Accounts Payable or Cash)
            $journalEntry->lines()->create([
                'account_id' => $creditAccount->id,
                'debit' => 0,
                'credit' => $purchaseOrder->total_amount,
                'description' => "Purchase from {$supplier?->name} on {$paymentTerms} terms",
            ]);

            DB::commit();

            Log::info("Journal entry created for purchase order {$purchaseOrder->po_number}", [
                'purchase_order_id' => $purchaseOrder->id,
                'journal_entry_id' => $journalEntry->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create journal entry for purchase order {$purchaseOrder->po_number}: {$e->getMessage()}", [
                'purchase_order_id' => $purchaseOrder->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Don't throw exception to prevent purchase order update from failing
        }
    }

    /**
     * Find or create an account by name.
     */
    protected function findOrCreateAccount(string $name, string $type, string $normalBalance): Account
    {
        $account = Account::where('name', $name)->first();

        if (!$account) {
            $account = Account::create([
                'name' => $name,
                'type' => $type,
                'normal_balance' => $normalBalance,
                'code' => $this->generateAccountCode($type),
                'balance' => 0,
                'is_active' => true,
                'description' => "Auto-created account for {$name}",
            ]);
        }

        return $account;
    }

    /**
     * Generate entry number in format JE-YYYYMMDD-XXXXXX.
     */
    protected function generateEntryNumber(): string
    {
        return 'JE-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Generate account code based on type.
     */
    protected function generateAccountCode(string $type): string
    {
        $prefix = match($type) {
            'asset' => '1',
            'liability' => '2',
            'equity' => '3',
            'revenue' => '4',
            'expense' => '5',
            default => '9',
        };

        $lastAccount = Account::where('type', $type)
            ->where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastAccount) {
            $lastNumber = (int) substr($lastAccount->code, 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1000;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
