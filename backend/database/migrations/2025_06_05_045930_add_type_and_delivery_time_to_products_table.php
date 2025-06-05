<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('type')->default('instock')->comment('preorder - под заказ, rent - в аренду, instock - в наличии');
            $table->integer('delivery_time')->default(0)->comment('Время доставки в днях (только для preorder)');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['type', 'delivery_time']);
        });
    }
};