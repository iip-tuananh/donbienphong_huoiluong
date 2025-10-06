<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('license_plate');
            $table->unsignedBigInteger('license_plate_id')->index();
            $table->foreign('license_plate_id')->references('id')->on('license_plates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['license_plate_id']);
            $table->dropColumn('license_plate_id');
            $table->string('license_plate');
        });
    }
}
