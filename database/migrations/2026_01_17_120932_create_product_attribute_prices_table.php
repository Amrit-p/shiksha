<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
          if (!Schema::hasTable('product_attribute_prices')) {
        Schema::create('product_attribute_prices', function (Blueprint $table) {
            $table->id();

            // Core links
            $table->unsignedBigInteger('product_id')->unsigned()->nullable();
            $table->unsignedBigInteger('variant_id')->nullable();

            // Attribute info
            $table->unsignedBigInteger('attribute_id')->unsigned()->nullable();
            $table->unsignedBigInteger('attribute_option_id')->nullable();

            // Inventory
            $table->decimal('mrp', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('stock')->default(0);

            // 🔥 IMPORTANT: image per attribute / variant
            $table->string('image')->nullable();

            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            // Indexes (fast + clean)
            $table->index('product_id');
            $table->index('variant_id');
            $table->index('attribute_id');
            $table->index('attribute_option_id');
        });
    }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_prices');
    }
};
