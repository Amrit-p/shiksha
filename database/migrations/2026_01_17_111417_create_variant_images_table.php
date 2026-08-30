<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
          if (!Schema::hasTable('variant_images')) {
        Schema::create('variant_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_id');
            $table->string('image');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->index('variant_id');
        });
    }
    }
    
    public function down(): void
    {
        Schema::dropIfExists('variant_images');
    }
};

