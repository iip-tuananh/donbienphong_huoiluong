<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->bigIncrements('id');

			$table->string('column_name');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();

            $table->unsignedBigInteger('model_id');
            $table->string('model_type');

            $table->unsignedBigInteger('version_id');
            $table->foreign('version_id')->references('id')->on('versions');

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
        Schema::dropIfExists('histories');
    }
}
