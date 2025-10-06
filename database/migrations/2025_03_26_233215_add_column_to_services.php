<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->integer('cumulative_point')->nullable()->change();
            $table->string('slug')->nullable();
            $table->unsignedBigInteger('base_price')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->string('time')->nullable();
            $table->string('description')->nullable();
            $table->longText('content')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('base_price');
            $table->dropColumn('price');
            $table->dropColumn('description');
            $table->dropColumn('content');
        });
    }
}
