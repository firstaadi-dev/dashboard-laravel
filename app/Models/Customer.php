<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'company',
        'address',
        'city',
        'province',
        'postal_code',
        'tax_id',
        'type',
        'credit_limit',
        'current_balance',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the transactions for the customer.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
