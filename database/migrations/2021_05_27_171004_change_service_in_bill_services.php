<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeServiceInBillServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_services', function (Blueprint $table) {
            $table->dropForeign(['g7_service_id']);
			$table->dropColumn('g7_service_id');
			$table->dropForeign('bs_gsvcg');
			$table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services');
			$table->foreign('group_id', 'bs_svcg')->references('id')->on('service_vehicle_category_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_services', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
			$table->dropColumn('service_id');
			$table->unsignedBigInteger('g7_service_id')->nullable();
            $table->foreign('g7_service_id')->references('id')->on('g7_services');
			$table->dropForeign('bs_svcg');
			$table->foreign('group_id', 'bs_gsvcg')->references('id')->on('g7_service_vehicle_category_groups');
        });
    }
}
