<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->text('description_eng')->nullable()->change();
            $table->text('content_eng')->nullable()->change();
        });

        Schema::table('blocks', function (Blueprint $table) {
            $table->text('body_eng')->nullable()->change();
        });

        Schema::table('service_spa', function (Blueprint $table) {
            $table->text('body_eng')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
