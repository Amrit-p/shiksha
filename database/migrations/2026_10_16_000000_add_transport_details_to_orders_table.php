<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('transport_gst')->nullable()->after('transport_name');
            $table->string('transport_mobile', 15)->nullable()->after('transport_gst');
            $table->string('contact_person')->nullable()->after('transport_mobile');
            $table->string('delivery_type', 20)->nullable()->after('contact_person'); // partial | single_lot
            $table->date('dispatch_date')->nullable()->after('delivery_type');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['transport_gst', 'transport_mobile', 'contact_person', 'delivery_type', 'dispatch_date']);
        });
    }
};
