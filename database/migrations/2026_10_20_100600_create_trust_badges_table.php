<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Global set of trust badges shown as a small carousel on the salesman home and product pages.
        Schema::create('trust_badges', function (Blueprint $table) {
            $table->id();
            $table->string('title', 60);
            $table->string('subtitle', 120)->nullable();
            $table->string('icon');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trust_badges');
    }
};
