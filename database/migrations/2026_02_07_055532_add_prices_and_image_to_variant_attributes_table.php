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
        Schema::table('variant_attributes', function (Blueprint $table) {
            //
            // $table->decimal('mrp', 10, 2)->nullable()->after('attribute_value');
            $table->tinyInteger('is_por')->default(0)->after('sell_price')->comment('1 = Price on Request, 0 = Normal pricing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variant_attributes', function (Blueprint $table) {
            //
            $table->dropColumn('is_por');

        });
    }
};
