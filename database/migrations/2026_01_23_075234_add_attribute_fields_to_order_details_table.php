<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {

            // Variant attribute support
            $table->unsignedBigInteger('variant_attribute_id')
                  ->nullable()
                  ->after('variant_id');

            // Line total (price * qty)
            $table->decimal('total', 10, 2)
                  ->default(0)
                  ->after('qty');
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn([
                'variant_attribute_id',
                'total',
            ]);
        });
    }
};
