<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds support for 4-5 step variant products while maintaining
     * 100% backward compatibility with existing 2-3 step products.
     * 
     * Product Types:
     * - 1 = Simple (no variants)
     * - 3 = Variant (2-5 configurable steps)
     */
    public function up()
    {
        // Table 1: Variant Step Configurations
        // Stores configuration for products that need 4-5 steps
        // Only products using extended steps will have records here
        Schema::create('variant_step_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->tinyInteger('total_steps')->default(2)->comment('Total steps: 2-5');
            $table->json('step_labels')->nullable()->comment('Custom labels for each step');
            $table->timestamps();
            
            // Foreign key to products table
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
            
            // Index for faster lookups
            $table->index('product_id');
        });
        
        // Table 2: Variant Step Values
        // Stores the actual values for intermediate steps 3 and 4
        // Links to the variants table
        Schema::create('variant_step_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_id');
            $table->tinyInteger('step_number')->comment('Which step: 3 or 4');
            $table->unsignedBigInteger('step_value_id')->nullable()->comment('FK to variant_step_options');
            $table->string('step_value_text')->nullable()->comment('Custom text value if not using predefined option');
            $table->timestamps();
            
            // Foreign key to variants table
            $table->foreign('variant_id')
                  ->references('id')
                  ->on('variants')
                  ->onDelete('cascade');
            
            // Indexes for faster filtering
            $table->index(['variant_id', 'step_number']);
            $table->index('step_value_id');
        });
        
        // Table 3: Variant Step Options (Master Data)
        // Predefined options for steps 3 and 4 (like the attributes table)
        // Admins can create reusable step values here
        Schema::create('variant_step_options', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('step_number')->comment('Which step: 3 or 4');
            $table->string('name')->comment('Display name of the option');
            $table->string('value')->nullable()->comment('Optional value field');
            $table->tinyInteger('status')->default(1)->comment('1=Active, 0=Inactive');
            $table->timestamps();
            
            // Indexes for filtering
            $table->index(['step_number', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Drop in reverse order due to foreign keys
        Schema::dropIfExists('variant_step_values');
        Schema::dropIfExists('variant_step_configs');
        Schema::dropIfExists('variant_step_options');
    }
};