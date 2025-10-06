<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateG7ServiceVehicleCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g7_service_vehicle_categories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('g7_service_id');
            $table->foreign('g7_service_id')->references('id')->on('g7_services');

            $table->unsignedBigInteger('vehicle_category_id');
            $table->foreign('vehicle_category_id')->references('id')->on('vehicle_categories');

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
        Schema::dropIfExists('g7_service_vehicle_categories');
    }
}
