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
        if (!Schema::hasTable('banners')) {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('btn_text')->nullable();
            $table->string('btn_link')->nullable();
            $table->string('image')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
        });
    }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
