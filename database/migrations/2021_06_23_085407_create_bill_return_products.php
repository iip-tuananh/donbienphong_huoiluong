<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillReturnProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_return_products', function (Blueprint $table) {
            $table->bigIncrements('id');

			$table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')->references('id')->on('bill_returns');

			$table->unsignedBigInteger('bill_product_id');
            $table->foreign('bill_product_id')->references('id')->on('bill_products');

			$table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
			$table->string('product_name');
			$table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units');
			$table->string('unit_name');

			$table->decimal('qty', 10, 2);
			$table->decimal('price', 16, 2);
			$table->decimal('import_price', 16, 2);

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
        Schema::dropIfExists('bill_return_products');
    }
}
