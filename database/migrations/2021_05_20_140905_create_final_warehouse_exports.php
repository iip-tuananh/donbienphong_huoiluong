<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinalWarehouseExports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('final_warehouse_adjusts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('code')->unique();

            $table->text('note')->nullable();

            $table->unsignedBigInteger('g7_id');
            $table->foreign('g7_id')->references('id')->on('g7_infos');

            $table->unsignedBigInteger('created_by')->nullable();
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
        Schema::dropIfExists('final_warehouse_exports');
    }
}
