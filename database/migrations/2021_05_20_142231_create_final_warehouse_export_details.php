<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinalWarehouseExportDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('final_warehouse_adjust_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')->references('id')->on('final_warehouse_adjusts');

            $table->unsignedBigInteger('g7_product_id');
            $table->foreign('g7_product_id')->references('id')->on('g7_products');

            $table->decimal('change', 10, 2);

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
        Schema::dropIfExists('final_warehouse_export_details');
    }
}
