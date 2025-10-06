<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameEngToRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('name_eng')->nullable();
            $table->string('area_eng')->nullable();
            $table->string('view_eng')->nullable();
            $table->string('bedrooms_eng')->nullable();
            $table->string('maximum_occupancy_eng')->nullable();
            $table->text('description_eng')->nullable();
            $table->text('highlight_eng')->nullable();
            $table->text('amenities_eng')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            //
        });
    }
}
