<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColInTableProducts extends Migration
{

    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->longText('note');
            $table->unsignedInteger('points');
            $table->unsignedBigInteger('price');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
