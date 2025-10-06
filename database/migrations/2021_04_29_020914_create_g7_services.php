<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateG7Services extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g7_services', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->string('code');
            $table->integer('status')->default(1);
            $table->integer('cumulative_point')->default(0);

            $table->unsignedBigInteger('service_type_id');
            $table->foreign('service_type_id')->references('id')->on('service_types');

            $table->unsignedBigInteger('g7_id');
            $table->foreign('g7_id')->references('id')->on('g7_infos');

            $table->unsignedBigInteger('root_service_id')->nullable();
            $table->foreign('root_service_id')->references('id')->on('services');

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
        Schema::dropIfExists('g7_services');
    }
}
