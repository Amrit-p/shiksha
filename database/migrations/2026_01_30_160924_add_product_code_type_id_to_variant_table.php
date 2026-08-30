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
        Schema::table('variants', function (Blueprint $table) {
            //
              $table->unsignedBigInteger('product_code_type_id')
                  ->nullable()
                  ->after('variant_name_id');

            $table->foreign('product_code_type_id')
                  ->references('id')
                  ->on('product_code_types')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variant', function (Blueprint $table) {
            //
               $table->dropForeign(['product_code_type_id']);
            $table->dropColumn('product_code_type_id');
        });

    }
};
