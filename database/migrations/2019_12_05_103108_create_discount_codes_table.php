<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountCodesTable extends Migration
{
    public function up()
    {
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table
                ->string('code', 100)
                ->index();
            $table->unsignedTinyInteger('percent');
            $table->unsignedInteger('max');
            $table
                ->boolean('is_used')
                ->default(false);
            $table->date('expiration_date');
            $table
                ->unsignedBigInteger('user_id')
                ->nullable();
            $table
                ->timestamp('created_at')
                ->useCurrent();
            $table
                ->unsignedInteger('group_id')
                ->index();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('discount_codes');
    }
}
