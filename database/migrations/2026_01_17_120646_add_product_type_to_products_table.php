<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
          if (!Schema::hasTable('products')) {
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('product_type')
                  ->default(1)
                  ->comment('1 = simple, 2 = attribute_only, 3 = variant')
                  ->after('category_id');
        });
    }
    }
    
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_type');
        });
    }
};
