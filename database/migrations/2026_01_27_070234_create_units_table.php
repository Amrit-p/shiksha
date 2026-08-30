<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();        // Kilogram, Meter, Piece
            $table->string('short_name')->nullable();  // kg, m, pcs
            $table->string('multiplier')->nullable(); 
            $table->boolean('status')->default(1);
            $table->softDeletes(); // adds deleted_at

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
