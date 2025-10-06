<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBooking extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('booking_time');
            $table->string('mobile');
            $table->text('note')->nullable();

            $table->unsignedBigInteger('g7_id');
            $table->foreign('g7_id')->references('id')->on('g7_infos');

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');

            $table->integer('status')->default(1);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }

}
