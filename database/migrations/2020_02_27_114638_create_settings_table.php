<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table
                ->unsignedInteger('order_cancel_time')
                ->default(3600);
            $table->string('discount_mode');
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
