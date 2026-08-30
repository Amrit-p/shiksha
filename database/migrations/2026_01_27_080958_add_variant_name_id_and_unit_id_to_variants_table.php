<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('variants', function (Blueprint $table) {

            $table->foreignId('variant_name_id')
                  ->nullable()
                  ->after('product_id')
                  ->constrained('variant_names')
                  ->nullOnDelete();

            $table->foreignId('unit_id')
                  ->nullable()
                  ->after('variant_name_id')
                  ->constrained('units')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            $table->dropForeign(['variant_name_id']);
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['variant_name_id', 'unit_id']);
        });
    }
};
