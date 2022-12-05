<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transactionable_type');
            $table->unsignedBigInteger('transactionable_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('amount');
            $table->tinyInteger('type');
            $table->boolean('is_verified');
            $table
                ->string('code')
                ->unique();
            $table->string('device');
            $table->timestamps();

            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
