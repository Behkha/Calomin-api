<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('delivery_method_id');
            $table->tinyInteger('status');
            $table->unsignedBigInteger('address_id');
            $table
                ->string('code')
                ->unique();
            $table->timestamps();
            $table->unsignedInteger('price');
            $table
                ->unsignedInteger('price_after_discount')
                ->nullable();
            $table->string('payment_method');

            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users');

            $table
                ->foreign('delivery_method_id')
                ->references('id')
                ->on('delivery_methods');

            $table
                ->foreign('address_id')
                ->references('id')
                ->on('addresses');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
