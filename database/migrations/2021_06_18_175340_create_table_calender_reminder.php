<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCalenderReminder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_reminders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('reminder_date');
            $table->date('registry_date')->nullable();
            $table->integer('reminder_type');

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');

            $table->unsignedBigInteger('car_id');
            $table->foreign('car_id')->references('id')->on('cars');

            $table->text('note')->nullable();

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
        Schema::dropIfExists('calender_reminders');
    }
}
