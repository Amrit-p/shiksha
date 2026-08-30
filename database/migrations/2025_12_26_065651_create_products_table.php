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
    if (!Schema::hasTable('products')) {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')
                ->on('categories')->references('id')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->nullable()->default(0);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('is_featured')->default(0);
            $table->integer('is_discounted')->default(0);
            $table->integer('is_tranding')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }
}


  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('products');
  }
};
