<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAndAlterColInG7Infos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('g7_infos', function (Blueprint $table) {
            $table->longText('note')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['g7_info_id']);
            $table->dropColumn('g7_info_id');
            $table->unsignedBigInteger('g7_id')->nullable();
            $table->foreign('g7_id')->references('id')->on('g7_infos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('g7_infos', function (Blueprint $table) {
            $table->dropColumn('note');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['g7_id']);
            $table->dropColumn('g7_id');
            $table->unsignedBigInteger('g7_info_id')->nullable();
            $table->foreign('g7_info_id')->references('id')->on('g7_infos');
        });
    }
}
