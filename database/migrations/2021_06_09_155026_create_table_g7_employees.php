<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableG7Employees extends Migration
{
    public function up()
    {
        Schema::create('g7_employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('address');
            $table->string('mobile');
            $table->string('email');
            $table->boolean('gender');
            $table->date('birth_day');
            $table->date('start_date');
            $table->unsignedBigInteger('g7_id');
            $table->foreign('g7_id')->references('id')->on('g7_infos');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('g7_employees');
    }
}
