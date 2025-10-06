<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColInVehicleType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_types', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_category_id')->nullable();
            $table->foreign('vehicle_category_id')->references('id')->on('vehicle_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_types', function (Blueprint $table) {
            $table->dropForeign(['vehicle_category_id']);
            $table->dropColumn('vehicle_category_id');
        });
    }
}
