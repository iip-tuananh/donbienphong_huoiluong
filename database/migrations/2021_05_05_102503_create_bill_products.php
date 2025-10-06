<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_products', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')->references('id')->on('bills');

            $table->unsignedBigInteger('g7_product_id');
            $table->foreign('g7_product_id')->references('id')->on('g7_products');
            $table->string('name');
            $table->string('code');

            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->string('unit_name');

            $table->decimal('qty', 10, 2);
            $table->decimal('price', 16, 2);
            $table->decimal('total_cost', 16, 2);

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
        Schema::dropIfExists('bill_products');
    }
}
