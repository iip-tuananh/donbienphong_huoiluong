<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropG7ServiceVehicleCategoryProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('g7_service_vehicle_category_products');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('g7_service_vehicle_category_products', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->decimal('qty', 10, 2);
            $table->decimal('service_price', 16, 2);

            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id', 'gsvcp_gsvc')->references('id')->on('g7_service_vehicle_categories');

            $table->unsignedBigInteger('g7_service_id');
            $table->foreign('g7_service_id', 'gsvcp_gs')->references('id')->on('g7_services');

            $table->unsignedBigInteger('g7_product_id');
            $table->foreign('g7_product_id')->references('id')->on('g7_products');

            $table->timestamps();
        });
    }
}
