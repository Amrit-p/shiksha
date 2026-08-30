<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Updates variant_custom_fields to link to custom_field_types instead of storing field_name as text
     */
    public function up()
    {
        Schema::table('variant_custom_fields', function (Blueprint $table) {
            // Drop old field_name column
            $table->dropColumn('field_name');
            
            // Add foreign key to custom_field_types
            $table->unsignedBigInteger('custom_field_type_id')->after('variant_id');
            
            $table->foreign('custom_field_type_id')
                  ->references('id')
                  ->on('custom_field_types')
                  ->onDelete('cascade');
            
            $table->index(['variant_id', 'custom_field_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('variant_custom_fields', function (Blueprint $table) {
            $table->dropForeign(['custom_field_type_id']);
            $table->dropColumn('custom_field_type_id');
            $table->string('field_name')->after('variant_id');
        });
    }
};