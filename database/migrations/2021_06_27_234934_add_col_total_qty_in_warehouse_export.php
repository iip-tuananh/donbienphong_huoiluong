<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColTotalQtyInWarehouseExport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_exports', function (Blueprint $table) {
            $table->decimal('total_qty', 16, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_exports', function (Blueprint $table) {
            $table->dropColumn('total_qty');
        });
    }
}
