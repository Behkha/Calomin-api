<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeCategoryTable extends Migration
{
    public function up()
    {
        Schema::create('attribute_category', function (Blueprint $table) {
            $table->unsignedInteger('attribute_id');
            $table->unsignedBigInteger('category_id');

            $table
                ->foreign('attribute_id')
                ->references('id')
                ->on('attributes');
            $table
                ->foreign('category_id')
                ->references('id')
                ->on('categories');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attribute_category');
    }
}
