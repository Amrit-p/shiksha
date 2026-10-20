<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per delivery round (an order can be delivered in several parts).
        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->date('delivered_on');
            $table->string('note', 1000)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Quantity delivered per order line in that round. line_key points into orders.product_json:
        // "3" = simple product at index 3, "3-1" = attribute 1 of the variant product at index 3.
        Schema::create('order_delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_delivery_id')->constrained('order_deliveries')->cascadeOnDelete();
            $table->string('line_key', 20);
            $table->unsignedInteger('qty');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_delivery_items');
        Schema::dropIfExists('order_deliveries');
    }
};
