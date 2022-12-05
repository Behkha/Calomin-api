<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('commented_by');

            $table->string('commentable_type');

            $table->unsignedBigInteger('commentable_id');

            $table->string('body');

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('commented_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
