<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

			$table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
			$table->unsignedBigInteger('g7_id')->nullable();
            $table->foreign('g7_id')->references('id')->on('g7_infos');
			$table->text('content');
			$table->string('link')->nullable();
			$table->dateTime('time');

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
        Schema::dropIfExists('activity_logs');
    }
}
