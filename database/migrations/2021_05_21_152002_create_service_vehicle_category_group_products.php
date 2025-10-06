<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceVehicleCategoryGroupProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_vehicle_category_group_products', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->decimal('qty', 10, 2);

            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id', 'svcgp_svcg')->references('id')->on('service_vehicle_category_groups');

            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services');

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_vehicle_category_group_products');
    }
}
