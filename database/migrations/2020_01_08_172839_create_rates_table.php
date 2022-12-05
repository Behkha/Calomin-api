<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatesTable extends Migration
{
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('rated_by');

            $table->float('score', 2, 1);

            $table->string('rateable_type');

            $table->unsignedBigInteger('rateable_id');

            $table->foreign('rated_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rates');
    }
}
