<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToStockLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_export_detail_id')->nullable();
            $table->foreign('warehouse_export_detail_id')->references('id')->on('warehouse_export_details');

			$table->decimal('value_before', 16, 2)->nullable();
			$table->decimal('value_after', 16, 2)->nullable();
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
            $table->dropForeign(['warehouse_export_detail_id']);
			$table->dropColumn('value_before');
			$table->dropColumn('value_after');
        });
    }
}
