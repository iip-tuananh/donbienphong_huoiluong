<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductGalleries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_galleries', function (Blueprint $table) {
            $table->bigIncrements('id');

			$table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');

            $table->integer('sort')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_galleries');
    }
}
