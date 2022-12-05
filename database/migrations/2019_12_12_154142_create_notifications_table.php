<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('notificationable_type')
                ->nullable();

            $table->unsignedBigInteger('notificationable_id')
                ->nullable();
            
            $table->string('user_phone');

            $table->string('body')
                ->nullable();
            
            $table->boolean('is_read')
                ->default(false);
            
            $table->timestamp('created_at')
                ->useCurrent();

            $table->foreign('user_phone')
                ->references('phone')
                ->on('users')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
