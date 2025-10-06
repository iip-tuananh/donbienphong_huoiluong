<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldInServiceVehicleCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_vehicle_categories', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('service_price');
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_vehicle_categories', function (Blueprint $table) {
            $table->integer('status')->default(1);
            $table->decimal('service_price', 16, 2)->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }
}
