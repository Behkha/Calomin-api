<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('name');

            $table->string('email')
                ->unique();

            $table->string('phone')
                ->unique();

            $table->string('password');

            $table->string('profile_picture')
                ->nullable();

            $table->string('id_code')
                ->nullable();

            $table->string('home_phone')
                ->nullable();

            $table->boolean('want_notification')
                ->default(false);

            $table->date('birth_date')
                ->nullable();

            $table->tinyInteger('sex')
                ->nullable();
            
            $table->unsignedInteger('state_id')
                ->nullable();

            $table->unsignedBigInteger('city_id')
                ->nullable();


            $table->boolean('is_verified')
                ->default(false);
            
            $table->date('registered_at')
                ->nullable();
                
            $table->timestamps();

            $table->foreign('state_id')
                ->references('id')
                ->on('states');

            $table->foreign('city_id')
                ->references('id')
                ->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
