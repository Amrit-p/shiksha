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
        Schema::table('carts', function (Blueprint $table) {
            //
            // existing product_attr_id ko hata rahe hain
            if (Schema::hasColumn('carts', 'product_attr_id')) {
                $table->dropColumn('product_attr_id');
            }

            // new proper columns
            // $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            // $table->unsignedBigInteger('variant_attribute_id')->nullable()->after('variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            //
              $table->dropColumn(['variant_id', 'variant_attribute_id']);
            $table->unsignedBigInteger('product_attr_id')->nullable();
        });
    }
};
