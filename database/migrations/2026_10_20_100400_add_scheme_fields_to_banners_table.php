<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
            $table->date('starts_at')->nullable()->after('status');
            $table->date('ends_at')->nullable()->after('starts_at');
            $table->unsignedInteger('sort_order')->default(0)->after('ends_at');
            // Also show in the "Current Schemes" popup on the salesman home page.
            $table->boolean('show_in_popup')->default(false)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['title', 'starts_at', 'ends_at', 'sort_order', 'show_in_popup']);
        });
    }
};
