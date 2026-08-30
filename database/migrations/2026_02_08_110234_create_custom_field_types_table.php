<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates custom_field_types table - master list of available custom field types
     * Admin can add/edit field types like: Shape, Size, Material, Finish, etc.
     */
    public function up()
    {
        Schema::create('custom_field_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Shape", "Size", "Material"
            $table->string('slug')->unique(); // e.g., "shape", "size", "material"
            $table->text('description')->nullable(); // Optional description
            $table->integer('display_order')->default(0); // For sorting
            $table->tinyInteger('status')->default(1); // 1=Active, 0=Inactive
            $table->timestamps();
            
            $table->index(['status', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('custom_field_types');
    }
};