<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Constraint\Constraint;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    if (!Schema::hasTable('variants')) {
    Schema::create('variants', function (Blueprint $table) {
      $table->id();
      // $table->foreignId('product_id')->constrained()->cascadeOnDelete();
      $table->string('name')->nullable();

      $table->foreignId('product_id')->constrained('products')->cascadeOnDelete()->cascadeOnUpdate();

      $table->foreignId('body_color')->nullable()->constrained('colors')->nullOnDelete()->cascadeOnUpdate();


      $table->string('custom_attr')->nullable();

      // Identification
      $table->string('catalog_number')->nullable(); // Cat. No.
      $table->string('sku')->nullable()->unique();
      // $table->string('barcode')->nullable();
      // $table->string('hsn_code')->nullable();

      // Technical Specs
      $table->text('short_description')->nullable();
      $table->string('wattage')->nullable();
      $table->string('voltage')->nullable();
      $table->string('dimension')->nullable();
      $table->string('material')->nullable();
      $table->string('color')->nullable();

      $table->string('cct')->nullable(); //other will be checked button where will i chaked it then  open input box ;

      // $table->string('beam_angle')->nullable();
      // $table->string('ip_rating')->nullable();
      $table->string('weight')->nullable();
      // Light-specific dimensions
      $table->integer('outer_diameter')->default(0)->nullable();
      $table->integer('inner_diameter')->default(0)->nullable();
      $table->integer('height')->default(0)->nullable();


      // Pricing
      $table->decimal('mrp', 10, 2)->nullable();
      $table->decimal('price', 10, 2)->nullable();
      // $table->decimal('dealer_price', 10, 2)->nullable();
      // $table->decimal('gst_percent', 5, 2)->nullable();

      // Inventory & UI
      $table->integer('stock')->default(0);
      $table->string('image')->nullable();
      // $table->boolean('is_default')->default(false);
      $table->boolean('status')->default(1);

      $table->timestamps();
   });
  }
  }
  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('variants');
  }
};
