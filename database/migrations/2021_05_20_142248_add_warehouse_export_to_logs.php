<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseExportToLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('final_warehouse_adjust_detail_id')->nullable();
            $table->foreign('final_warehouse_adjust_detail_id', 'st_fwed')->references('id')->on('final_warehouse_adjust_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->dropForeign('st_fwed');
            $table->dropColumn('final_warehouse_adjust_detail_id');
        });
    }
}
