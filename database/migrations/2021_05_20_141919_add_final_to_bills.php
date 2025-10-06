<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinalToBills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->unsignedBigInteger('final_warehouse_adjust_id')->nullable();
            $table->foreign('final_warehouse_adjust_id')->references('id')->on('final_warehouse_adjusts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['final_warehouse_adjust_id']);
            $table->dropColumn('final_warehouse_adjust_id');
        });
    }
}
