<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->enum('subtype', [
                'current_asset', 'fixed_asset', 'other_asset',
                'current_liability', 'long_term_liability',
                'owner_equity', 'retained_earnings',
                'operating_revenue', 'other_revenue',
                'operating_expense', 'other_expense'
            ])->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
