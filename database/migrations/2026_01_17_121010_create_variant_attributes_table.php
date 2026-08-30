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
          if (!Schema::hasTable('variant_attributes')) {
        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('variant_id');
            $table->unsignedBigInteger('attribute_id');
            $table->unsignedBigInteger('attribute_option_id')->nullable();
            $table->string('attribute_value')->nullable();
            $table->string('image')->nullable();


            $table->timestamps();

            $table->foreign('variant_id')
                ->references('id')
                ->on('variants')
                ->onDelete('cascade');

            $table->foreign('attribute_id')
                ->references('id')
                ->on('attributes')
                ->onDelete('cascade');

            $table->foreign('attribute_option_id')
                ->references('id')
                ->on('attribute_options')
                ->onDelete('cascade');

            $table->index(['variant_id', 'attribute_id']);
        });
    }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_attributes');
    }
};
