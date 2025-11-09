<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'reference_number',
        'type',
        'quantity',
        'previous_stock',
        'new_stock',
        'reference_type',
        'reference_id',
        'notes',
        'movement_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'previous_stock' => 'decimal:2',
        'new_stock' => 'decimal:2',
        'movement_date' => 'datetime',
    ];

    /**
     * Get the product that owns the stock movement.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who recorded the stock movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent referenceable model (PurchaseOrder, Transaction, etc).
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
