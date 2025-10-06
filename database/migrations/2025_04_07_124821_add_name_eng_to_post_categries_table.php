<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameEngToPostCategriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_categories', function (Blueprint $table) {
            $table->string('name_eng')->after('name')->nullable();
            $table->text('intro_eng')->after('name_eng')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_categories', function (Blueprint $table) {
            //
        });
    }
}
