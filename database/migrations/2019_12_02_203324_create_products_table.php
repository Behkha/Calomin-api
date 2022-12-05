<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table
                ->string('title')
                ->index();
            $table
                ->json('gallery')
                ->nullable();
            $table->unsignedInteger('price');
            $table->unsignedInteger('purchased_price');
            $table->unsignedInteger('quantity');
            $table->text('description')->nullable();
            $table->integer('view_count')->unsigned()->default(0);
            $table->unsignedBigInteger('category_id');
            // Tedade Kharidha Vase Takhfif
            $table->unsignedInteger('counter_sales');
            // Shomarande Baraye Takhfif Har Mahsool Be Saat
            $table->unsignedInteger('bonus');
            $table->timestamps();
            $table->unsignedInteger('brand_id');
            $table
                ->string('code')
                ->unique();
            $table
                ->boolean('has_tax')
                ->default(false);
            $table
                ->unsignedInteger('mass')
                ->nullable();

            $table
                ->foreign('brand_id')
                ->references('id')
                ->on('brands');
            $table
                ->unsignedInteger('counter')
                ->nullable()
                ->default(null);
            $table
                ->timestamp('counter_created_at')
                ->nullable()
                ->default(null);

            $table
                ->foreign('category_id')
                ->references('id')
                ->on('categories');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
