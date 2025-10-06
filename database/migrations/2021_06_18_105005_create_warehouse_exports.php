<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseExports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_exports', function (Blueprint $table) {
            $table->bigIncrements('id');

			$table->string('code')->unique();
			$table->integer('type');
			$table->text('note')->nullable();

			$table->unsignedBigInteger('bill_id')->nullable();
            $table->foreign('bill_id')->references('id')->on('bills');

			$table->unsignedBigInteger('g7_id');
            $table->foreign('g7_id')->references('id')->on('g7_infos');

			$table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
			$table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');

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
        Schema::dropIfExists('warehouse_exports');
    }
}
