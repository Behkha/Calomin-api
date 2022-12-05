<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeProductTable extends Migration
{
    public function up()
    {
        Schema::create('attribute_product', function (Blueprint $table) {           
            $table->unsignedInteger('attribute_id');
            $table->unsignedBigInteger('product_id');
            $table->string('value');

            $table
                ->foreign('attribute_id')
                ->references('id')
                ->on('attributes')
                ->onDelete('cascade');

            $table
                ->foreign('product_id')
                ->references('id')
                ->on('products');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attribute_product');
    }
}
