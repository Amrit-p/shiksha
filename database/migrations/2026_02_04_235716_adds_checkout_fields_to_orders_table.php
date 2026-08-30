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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_gst')->default(0)->after('payment_type');
            $table->boolean('is_transport')->default(0)->after('is_gst');
            $table->string('transport_name')->nullable()->after('is_transport');
            $table->string('landmark')->nullable()->after('transport_name');

            $table->integer('credit_days')->nullable()->after('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropcolumn(['is_gst', 'is_transport', 'transport_name', 'landmark', 'credit_days']);
        });
    }
};
