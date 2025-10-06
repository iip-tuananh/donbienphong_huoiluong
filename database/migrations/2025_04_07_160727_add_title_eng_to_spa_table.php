<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleEngToSpaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spa', function (Blueprint $table) {
            $table->string('title_eng')->nullable();
            $table->string('intro_eng')->nullable();
            $table->string('body_eng')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('spa', function (Blueprint $table) {
            //
        });
    }
}
