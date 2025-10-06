<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title_short');
            $table->string('title')->nullable();
            $table->string('times');
            $table->string('start_off');
            $table->string('schedule');
            $table->decimal('price',16);
            $table->string('vehicle');
            $table->string('destination');
            $table->text('itinerary')->nullable();
            $table->text('beware')->nullable();
            $table->text('photos')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('cate_id');
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
        Schema::dropIfExists('tours');
    }
}
