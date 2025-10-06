<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateG7ServiceProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g7_service_products', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->decimal('qty', 10, 2);

            $table->unsignedBigInteger('g7_service_id');
            $table->foreign('g7_service_id')->references('id')->on('g7_services');

            $table->unsignedBigInteger('g7_product_id');
            $table->foreign('g7_product_id')->references('id')->on('g7_products');

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
        Schema::dropIfExists('g7_service_products');
    }
}
