<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Transaction;
use App\Notifications\TransactionCompletedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        // If transaction is created with status 'completed' (direct payment/lunas),
        // create journal entry immediately
        if ($transaction->status === 'completed') {
            $this->createJournalEntry($transaction);
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        // Check if status changed to 'completed'
        if ($transaction->isDirty('status') && $transaction->status === 'completed') {
            $this->createJournalEntry($transaction);

            // Optionally send transaction completed notification to customer
            // Uncomment the line below to enable email receipts
            // $this->sendTransactionReceipt($transaction);
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Create journal entry for completed transaction.
     */
    protected function createJournalEntry(Transaction $transaction): void
    {
        try {
            // Check if journal entry already exists for this transaction
            $existingEntry = JournalEntry::where('reference_type', Transaction::class)
                ->where('reference_id', $transaction->id)
                ->first();

            if ($existingEntry) {
                return; // Already processed
            }

            DB::beginTransaction();

            // Determine account based on payment method
            $debitAccountName = match($transaction->payment_method) {
                'bank_transfer', 'debit_card', 'credit_card' => 'Bank Account',
                default => 'Cash in Hand',
            };

            // Get or create accounts
            $debitAccount = $this->findOrCreateAccount($debitAccountName, 'asset', 'debit');
            $creditAccount = $this->findOrCreateAccount('Sales Revenue', 'revenue', 'credit');

            // Create journal entry
            $journalEntry = JournalEntry::create([
                'user_id' => $transaction->user_id,
                'entry_number' => $this->generateEntryNumber(),
                'entry_date' => $transaction->transaction_date,
                'reference_type' => Transaction::class,
                'reference_id' => $transaction->id,
                'description' => "Sales transaction {$transaction->transaction_number}",
                'status' => 'posted',
                'posted_at' => now(),
            ]);

            // Create debit line (Cash/Bank)
            $journalEntry->lines()->create([
                'account_id' => $debitAccount->id,
                'debit' => $transaction->total_amount,
                'credit' => 0,
                'description' => "Sales revenue received via {$transaction->payment_method}",
            ]);

            // Create credit line (Sales Revenue)
            $journalEntry->lines()->create([
                'account_id' => $creditAccount->id,
                'debit' => 0,
                'credit' => $transaction->total_amount,
                'description' => "Sales to {$transaction->customer_name}",
            ]);

            DB::commit();

            Log::info("Journal entry created for transaction {$transaction->transaction_number}", [
                'transaction_id' => $transaction->id,
                'journal_entry_id' => $journalEntry->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create journal entry for transaction {$transaction->transaction_number}: {$e->getMessage()}", [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Don't throw exception to prevent transaction update from failing
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

    /**
     * Send transaction receipt to customer email (optional).
     *
     * Note: This requires adding a 'customer_email' field to transactions table,
     * or retrieving email from a related Customer model.
     */
    protected function sendTransactionReceipt(Transaction $transaction): void
    {
        try {
            // Example 1: If you have customer_email field in transactions table
            // if (!empty($transaction->customer_email) && filter_var($transaction->customer_email, FILTER_VALIDATE_EMAIL)) {
            //     Notification::route('mail', $transaction->customer_email)
            //         ->notify(new TransactionCompletedNotification($transaction));
            //
            //     Log::info("Transaction receipt sent to customer", [
            //         'transaction_id' => $transaction->id,
            //         'customer_email' => $transaction->customer_email,
            //     ]);
            // }

            // Example 2: If you have a Customer relationship with email
            // if ($transaction->customer && !empty($transaction->customer->email)) {
            //     $transaction->customer->notify(new TransactionCompletedNotification($transaction));
            //
            //     Log::info("Transaction receipt sent to customer", [
            //         'transaction_id' => $transaction->id,
            //         'customer_id' => $transaction->customer->id,
            //     ]);
            // }
        } catch (\Exception $e) {
            Log::error("Failed to send transaction receipt: {$e->getMessage()}", [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw exception to prevent transaction update from failing
        }
    }
}
