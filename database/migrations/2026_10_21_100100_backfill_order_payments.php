<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Orders marked Paid before payment tracking existed get one verified full payment,
     * so they stay Paid (status is now derived from verified payments) and show in past collection.
     */
    public function up(): void
    {
        DB::table('orders')
            ->where('payment_status', 'paid')
            ->where(fn ($q) => $q->whereNull('order_status')->orWhere('order_status', '!=', 'cancelled'))
            ->whereNotExists(fn ($q) => $q->from('order_payments')->whereColumn('order_payments.order_id', 'orders.id'))
            ->orderBy('id')
            ->chunk(500, function ($orders) {
                DB::table('order_payments')->insert($orders->map(fn ($order) => [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'amount' => $order->total_amount,
                    'paid_on' => substr((string) ($order->updated_at ?? $order->created_at ?? now()), 0, 10),
                    'mode' => 'other',
                    'note' => 'Marked paid before payment tracking.',
                    'status' => 'verified',
                    'reviewed_at' => now(),
                    'is_backfill' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all());
            });
    }

    public function down(): void
    {
        DB::table('order_payments')->where('is_backfill', true)->delete();
    }
};
