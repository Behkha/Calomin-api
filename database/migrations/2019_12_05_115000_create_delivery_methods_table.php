<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryMethodsTable extends Migration
{
    public function up()
    {
        Schema::create('delivery_methods', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')
                ->unique();
            
            $table->string('description')
                ->nullable();

            $table->unsignedInteger('price');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('delivery_methods');
    }
}
