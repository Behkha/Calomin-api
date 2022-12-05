<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactUsTable extends Migration
{
    public function up()
    {
        Schema::create('contact_us', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table
                ->unsignedInteger('subject_id')
                ->nullable();
            $table->string('body', 2000);
            $table
                ->timestamp('created_at')
                ->useCurrent();

            $table
                ->foreign('subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_us');
    }
}
