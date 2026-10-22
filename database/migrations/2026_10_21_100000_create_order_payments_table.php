<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Money received against an order. Salesmen log it (pending), the super admin verifies it.
        // Only verified rows count as collection and drive orders.payment_status.
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // the order's salesman
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('paid_on');
            $table->string('mode', 20);
            $table->string('reference', 100)->nullable();
            $table->text('note')->nullable();
            $table->string('proof_path')->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_reason')->nullable();
            $table->boolean('is_backfill')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'status', 'paid_on']);
            $table->index(['order_id', 'status']);
        });

        // Monthly targets per salesman. month = null is the standing default; a dated row overrides that month.
        Schema::create('salesman_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('month')->nullable();
            $table->decimal('sales_target', 12, 2)->nullable();
            $table->decimal('collection_target', 12, 2)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salesman_targets');
        Schema::dropIfExists('order_payments');
    }
};
