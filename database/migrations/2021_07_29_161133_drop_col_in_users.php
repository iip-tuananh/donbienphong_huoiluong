<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['g7_id']);
            $table->dropColumn('g7_id');
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('g7_id');
            $table->foreign('g7_id')->references('id')->on('g7_infos');
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('g7_employees');
        });
    }
}
