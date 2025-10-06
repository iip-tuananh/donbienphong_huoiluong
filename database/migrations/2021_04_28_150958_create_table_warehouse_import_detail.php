<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableWarehouseImportDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ware_house_import_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('qty');
            $table->decimal('price',16,4);
            $table->decimal('amount',16,4);
            $table->string('unit');

            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units');

            $table->unsignedBigInteger('ware_house_import_id');
            $table->foreign('ware_house_import_id')->references('id')->on('ware_house_imports');

            $table->unsignedBigInteger('g7_product_id');
            $table->foreign('g7_product_id')->references('id')->on('g7_products');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ware_house_import_detail');
    }
}
