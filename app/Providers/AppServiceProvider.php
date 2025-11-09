<?php

namespace App\Providers;

use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Observers\PurchaseOrderObserver;
use App\Observers\StockMovementObserver;
use App\Observers\TransactionObserver;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers for automatic journal entry creation
        Transaction::observe(TransactionObserver::class);
        PurchaseOrder::observe(PurchaseOrderObserver::class);
        StockMovement::observe(StockMovementObserver::class);

        // Uncomment to log all queries to storage/logs/laravel.log
        // DB::listen(function (QueryExecuted $query) {
        //     Log::info('Query', [
        //         'sql' => $query->sql,
        //         'bindings' => $query->bindings,
        //         'time' => $query->time . 'ms',
        //     ]);
        // });
    }
}
