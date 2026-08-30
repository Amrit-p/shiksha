<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stock is per variant attribute (e.g. per color: White, Black, Golden).
     * When a customer orders "White Fan", stock is deducted from the White variant_attribute.
     */
    public function up(): void
    {
        Schema::table('variant_attributes', function (Blueprint $table) {
            if (!Schema::hasColumn('variant_attributes', 'stock')) {
                $table->unsignedInteger('stock')->default(0)->after('is_por')
                    ->comment('Available quantity for this attribute option (e.g. per color)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variant_attributes', function (Blueprint $table) {
            if (Schema::hasColumn('variant_attributes', 'stock')) {
                $table->dropColumn('stock');
            }
        });
    }
};
