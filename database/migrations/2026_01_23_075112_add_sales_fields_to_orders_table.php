<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {

            // Sales / Shop details
            $table->string('shop_name')->nullable()->after('user_id');

            // Owner details
            $table->string('owner_name')->nullable()->after('shop_name');
            $table->string('owner_phone')->nullable()->after('owner_name');
            $table->string('owner_email')->nullable()->after('owner_phone');
            $table->text('owner_address')->nullable()->after('owner_email');

            $table->string('gst_number')->nullable()->after('owner_address');

            // Order meta
            $table->integer('total_qty')->default(0)->after('total_amount');
            $table->dateTime('punch_in_time')->nullable()->after('order_status');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shop_name',
                'owner_name',
                'owner_phone',
                'owner_email',
                'gst_number',
                'total_qty',
                'punch_in_time',
            ]);
        });
    }
};
