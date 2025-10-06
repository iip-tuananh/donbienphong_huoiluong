<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateG7ServiceVehicleCategoryGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g7_service_vehicle_category_groups', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->decimal('service_price', 16, 2);

            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id', 'gsvcg_gsvc')->references('id')->on('g7_service_vehicle_categories');

            $table->unsignedBigInteger('g7_service_id');
            $table->foreign('g7_service_id')->references('id')->on('g7_services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('g7_service_vehicle_category_groups');
    }
}
