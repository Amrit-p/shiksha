<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * When stock falls below min_stock, item appears in dashboard "Low Stock" list for restock.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'min_stock')) {
                $table->unsignedInteger('min_stock')->default(0)->after('stock')
                    ->comment('Alert when stock falls below this (0 = no alert)');
            }
        });

        Schema::table('variant_attributes', function (Blueprint $table) {
            if (!Schema::hasColumn('variant_attributes', 'min_stock')) {
                $table->unsignedInteger('min_stock')->default(0)->after('stock')
                    ->comment('Alert when stock falls below this (0 = no alert)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'min_stock')) {
                $table->dropColumn('min_stock');
            }
        });
        Schema::table('variant_attributes', function (Blueprint $table) {
            if (Schema::hasColumn('variant_attributes', 'min_stock')) {
                $table->dropColumn('min_stock');
            }
        });
    }
};
