<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\StockMovement;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class StockMovementObserver
{
    /**
     * Handle the StockMovement "created" event.
     */
    public function created(StockMovement $stockMovement): void
    {
        // Only create journal entry for adjustment type movements
        if ($stockMovement->type === 'adjustment') {
            $this->createJournalEntry($stockMovement);
        }

        // Check for low stock and send notification
        $this->checkLowStock($stockMovement);
    }

    /**
     * Handle the StockMovement "updated" event.
     */
    public function updated(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "deleted" event.
     */
    public function deleted(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "restored" event.
     */
    public function restored(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "force deleted" event.
     */
    public function forceDeleted(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Create journal entry for stock adjustment.
     */
    protected function createJournalEntry(StockMovement $stockMovement): void
    {
        try {
            // Check if journal entry already exists for this stock movement
            $existingEntry = JournalEntry::where('reference_type', StockMovement::class)
                ->where('reference_id', $stockMovement->id)
                ->first();

            if ($existingEntry) {
                return; // Already processed
            }

            DB::beginTransaction();

            // Load product relationship to get price
            $stockMovement->load('product');
            $product = $stockMovement->product;

            if (!$product) {
                Log::warning("Cannot create journal entry for stock movement: product not found", [
                    'stock_movement_id' => $stockMovement->id,
                ]);
                DB::rollBack();
                return;
            }

            // Calculate adjustment amount
            $adjustmentAmount = abs($stockMovement->quantity) * $product->price;

            // Determine if it's a positive or negative adjustment
            $isPositiveAdjustment = $stockMovement->new_stock > $stockMovement->previous_stock;

            // Get accounts
            $inventoryAccount = $this->findOrCreateAccount('Inventory', 'asset', 'debit');
            $adjustmentAccount = $this->findOrCreateAccount('Inventory Adjustment', 'expense', 'debit');

            // Create journal entry
            $adjustmentType = $isPositiveAdjustment ? 'increase' : 'decrease';
            $journalEntry = JournalEntry::create([
                'user_id' => $stockMovement->user_id,
                'entry_number' => $this->generateEntryNumber(),
                'entry_date' => $stockMovement->movement_date ?? now(),
                'reference_type' => StockMovement::class,
                'reference_id' => $stockMovement->id,
                'description' => "Stock adjustment for {$product->name}: {$adjustmentType} {$stockMovement->quantity}",
                'status' => 'posted',
                'posted_at' => now(),
            ]);

            if ($isPositiveAdjustment) {
                // Positive adjustment: Debit Inventory, Credit Inventory Adjustment
                $journalEntry->lines()->create([
                    'account_id' => $inventoryAccount->id,
                    'debit' => $adjustmentAmount,
                    'credit' => 0,
                    'description' => "Inventory increase for {$product->name}",
                ]);

                $journalEntry->lines()->create([
                    'account_id' => $adjustmentAccount->id,
                    'debit' => 0,
                    'credit' => $adjustmentAmount,
                    'description' => "Stock adjustment credit for {$product->name}",
                ]);
            } else {
                // Negative adjustment: Debit Inventory Adjustment, Credit Inventory
                $journalEntry->lines()->create([
                    'account_id' => $adjustmentAccount->id,
                    'debit' => $adjustmentAmount,
                    'credit' => 0,
                    'description' => "Stock adjustment expense for {$product->name}",
                ]);

                $journalEntry->lines()->create([
                    'account_id' => $inventoryAccount->id,
                    'debit' => 0,
                    'credit' => $adjustmentAmount,
                    'description' => "Inventory decrease for {$product->name}",
                ]);
            }

            DB::commit();

            Log::info("Journal entry created for stock adjustment {$stockMovement->reference_number}", [
                'stock_movement_id' => $stockMovement->id,
                'journal_entry_id' => $journalEntry->id,
                'adjustment_type' => $adjustmentType,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create journal entry for stock movement {$stockMovement->reference_number}: {$e->getMessage()}", [
                'stock_movement_id' => $stockMovement->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Don't throw exception to prevent stock movement creation from failing
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
     * Check if product stock is low and send notification.
     */
    protected function checkLowStock(StockMovement $stockMovement): void
    {
        try {
            $stockMovement->load('product');
            $product = $stockMovement->product;

            if (!$product) {
                return;
            }

            // Define minimum stock threshold
            $minimumStock = 10;

            // Only send notification if stock is at or below threshold
            if ($product->stock <= $minimumStock) {
                // Get users with permission to view inventory reports
                $users = User::permission('view_inventory_reports')->get();

                // If no users have that specific permission, notify managers and admins
                if ($users->isEmpty()) {
                    $users = User::role(['manager', 'admin', 'super_admin'])->get();
                }

                if ($users->isNotEmpty()) {
                    Notification::send($users, new LowStockNotification($product, $minimumStock));

                    Log::info("Low stock notification sent for product: {$product->name}", [
                        'product_id' => $product->id,
                        'current_stock' => $product->stock,
                        'minimum_stock' => $minimumStock,
                        'recipients_count' => $users->count(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to send low stock notification: {$e->getMessage()}", [
                'stock_movement_id' => $stockMovement->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw exception to prevent stock movement creation from failing
        }
    }
}
