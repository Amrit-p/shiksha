<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('variant_names', function (Blueprint $table) {
            $table->id();

            $table->string('name'); 
            // Example: Color, Size, Wattage, Capacity

            $table->boolean('status')->default(1);
            // 1 = Active, 0 = Inactive

            $table->softDeletes();
            // deleted_at for soft delete

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variant_names');
    }
};
