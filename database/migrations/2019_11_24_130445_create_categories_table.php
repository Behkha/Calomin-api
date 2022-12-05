<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table
                ->string('logo')
                ->nullable();
            $table
                ->unsignedBigInteger('parent_id')
                ->nullable();

            $table
                ->foreign('parent_id')
                ->references('id')
                ->on('categories');
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
