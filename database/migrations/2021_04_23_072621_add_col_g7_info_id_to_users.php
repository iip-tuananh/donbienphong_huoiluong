<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColG7InfoIdToUsers extends Migration
{

    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('g7_info_id')->nullable();
            $table->foreign('g7_info_id')->references('id')->on('g7_infos');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['g7_info_id']);
            $table->dropColumn('g7_info_id');
        });
    }
}
