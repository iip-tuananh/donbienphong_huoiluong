<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateG7ServiceVehicleCategoryGroupProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g7_service_vehicle_category_group_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('qty', 10, 2);

            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id', 'gsvcgp_gsvcg')->references('id')->on('g7_service_vehicle_category_groups');

            $table->unsignedBigInteger('g7_service_id');
            $table->foreign('g7_service_id')->references('id')->on('g7_services');

            $table->unsignedBigInteger('g7_product_id');
            $table->foreign('g7_product_id')->references('id')->on('g7_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('g7_service_vehicle_category_group_products');
    }
}
