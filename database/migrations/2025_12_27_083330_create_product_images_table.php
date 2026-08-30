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
    if (!Schema::hasTable('product_images')) {
    Schema::create('product_images', function (Blueprint $table) {
      $table->id();
      // $table->foreignId('product_id')->constrained()->cascadeOnDelete();

      $table->unsignedBigInteger('product_id');
      $table->foreign('product_id')
      ->on('products')
      ->references('id')
      ->onUpdate('cascade')
      ->onDelete('cascade');

      $table->string('product_images')->nullable(); // store file path or name
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
    Schema::dropIfExists('product_images');
  }
};
