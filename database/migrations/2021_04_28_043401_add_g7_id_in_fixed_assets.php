<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddG7IdInFixedAssets extends Migration
{

    public function up()
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->unsignedBigInteger('g7_id')->nullable();
            $table->foreign('g7_id')->references('id')->on('g7_infos');
        });
    }

    public function down()
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->dropForeign(['g7_id']);
            $table->dropColumn('g7_id');
        });
    }
}
