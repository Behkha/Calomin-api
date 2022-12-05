<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellersTable extends Migration
{
    public function up()
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');

            $table->text('story')->nullable();

            $table->string('profile_picture')->nullable();

            $table->json('gallery')->nullable();

            $table->unsignedBigInteger('state_id');

            $table->unsignedBigInteger('city_id');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sellers');
    }
}
