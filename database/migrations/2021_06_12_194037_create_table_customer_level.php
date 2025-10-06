<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCustomerLevel extends Migration
{

    public function up()
    {
        Schema::create('customer_levels', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->unique();
            $table->integer('point');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_levels');
    }
}
