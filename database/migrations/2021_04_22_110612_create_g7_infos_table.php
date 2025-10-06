<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateG7InfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g7_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('adress')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('status')->default(1);

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('province_id');
            $table->foreign('province_id')->references('id')->on('provinces');

            $table->unsignedBigInteger('district_id');
            $table->foreign('district_id')->references('id')->on('districts');

            $table->unsignedBigInteger('ward_id');
            $table->foreign('ward_id')->references('id')->on('wards');

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
        Schema::dropIfExists('g7_infos');
    }
}
