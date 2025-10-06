<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSuppliers extends Migration
{

    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('code')->unique();
            $table->string('name');
            $table->string('mobile')->nullable();
            $table->string('address')->nullable();
            $table->integer('status')->default(1);

            $table->unsignedBigInteger('g7_id');
            $table->foreign('g7_id')->references('id')->on('g7_infos');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}
