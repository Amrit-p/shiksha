<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('description')->nullable();
            $table->string('type', 10); // percent | flat
            $table->decimal('value', 10, 2);
            $table->decimal('max_discount', 10, 2)->nullable();      // cap for percent coupons
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->unsignedInteger('usage_limit_total')->nullable();
            $table->unsignedInteger('usage_limit_per_shop')->nullable(); // shop = owner phone / GST number
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal_amount', 10, 2)->nullable()->after('total_qty');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('subtotal_amount');
            $table->foreignId('coupon_id')->nullable()->after('discount_amount')->constrained('coupons')->nullOnDelete();
            $table->string('coupon_code', 40)->nullable()->after('coupon_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
            $table->dropColumn(['subtotal_amount', 'discount_amount', 'coupon_code']);
        });

        Schema::dropIfExists('coupons');
    }
};
