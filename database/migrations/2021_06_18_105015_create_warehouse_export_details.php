<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseExportDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_export_details', function (Blueprint $table) {
            $table->bigIncrements('id');

			$table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')->references('id')->on('warehouse_exports');

			$table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
			$table->string('product_name');
			$table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units');
			$table->string('unit_name');

			$table->decimal('qty', 10, 2);
			$table->decimal('export_price', 16, 2)->nullable();

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
        Schema::dropIfExists('warehouse_export_details');
    }
}
