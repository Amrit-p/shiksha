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
    if (!Schema::hasTable('orders')) {
    Schema::create('orders', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id');
      $table->foreign('user_id')
        ->on('users')
        ->references('id')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->string('name')->nullable();
      $table->string('email')->nullable();
      $table->string('mobile')->nullable();
      $table->string('address')->nullable();
      $table->string('city')->nullable();
      $table->string('pin_code')->nullable();
      $table->string('state')->nullable();
      $table->string('payment_type')->nullable(); //cod//gateway
      $table->string('payment_status');
      $table->string('payment_id')->nullable();
      $table->decimal('total_amount', 10, 2);
      $table->string('order_status');
      $table->timestamps();
    });
  }
  }
  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('orders');
  }
};
