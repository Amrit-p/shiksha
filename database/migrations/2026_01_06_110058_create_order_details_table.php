<?php
#Required It Product and Variant table order table;
# it secondary table of order table;
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
        if (!Schema::hasTable('order_details')) {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')
            ->on('orders')
            ->references('id')
            ->onDelete('cascade')
            ->onUpdate('cascade');


            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')
            ->on('products')
            ->references('id')
            ->onUpdate('cascade')
            ->onDelete('cascade');




            $table->unsignedBigInteger('variant_id')->nullable();
            $table->foreign('variant_id')
            ->references('id')
            ->on('variants')
            ->onUpdate('cascade')
            ->onDelete('cascade');



            $table->decimal('price', 10, 2); // ✅ correct for money
            $table->unsignedInteger('qty');  // ✅ qty cannot be negative
            $table->timestamps();
        });
    }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
