<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAccumulatePoints extends Migration
{

    public function up()
    {
        Schema::create('accumulate_points', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('value_to_point_rate');
            $table->integer('point_to_money_rate');
            $table->boolean('allow_pay')->default(1);
            $table->boolean('accumulate_pay_point')->default(0);

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
        Schema::dropIfExists('accumulate_points');
    }
}
