<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolivicyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polivicy_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('polivicy_id');
            $table->string('title');
            $table->text('content');
            $table->string('title_eng');
            $table->text('content_eng');
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('polivicy_details');
    }
}
