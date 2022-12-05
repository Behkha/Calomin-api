<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributesTable extends Migration
{
    public function up()
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table
                ->string('unit')
                ->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attributes');
    }
}
