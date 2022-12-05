<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoothsTable extends Migration
{
    public function up()
    {
        Schema::create('booths', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title');

            $table->text('description')->nullable();

            $table->bigInteger('seller_id')->unsigned();

            $table->string('logo')->nullable();

            $table->bigInteger('category_id')->unsigned();

            $table->timestamps();

            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('booths');
    }
}
