<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates variant_custom_fields table to store dynamic custom fields
     * for each variant (like Shape, Size, Material, etc.)
     */
    public function up()
    {
        Schema::create('variant_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_id');
            $table->string('field_name'); // e.g., "Shape", "Size", "Material"
            $table->string('field_value'); // e.g., "Round", "6 inch", "Plastic"
            $table->integer('field_order')->default(0); // Display order
            $table->timestamps();
            
            $table->foreign('variant_id')
                  ->references('id')
                  ->on('variants')
                  ->onDelete('cascade');
            
            $table->index(['variant_id', 'field_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('variant_custom_fields');
    }
};