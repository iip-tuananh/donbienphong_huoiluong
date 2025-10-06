<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('stock_id');
            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->decimal('qty_before', 16, 2);
            $table->decimal('change', 16, 2);
            $table->decimal('qty_after', 16, 2);

            $table->unsignedBigInteger('warehouse_import_detail_id')->nullable();
            $table->foreign('warehouse_import_detail_id', 'st_wid')->references('id')->on('ware_house_import_detail');

            $table->unsignedBigInteger('bill_export_product_id')->nullable();
            $table->foreign('bill_export_product_id', 'st_bep')->references('id')->on('bill_export_products');

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
        Schema::dropIfExists('stock_logs');
    }
}
