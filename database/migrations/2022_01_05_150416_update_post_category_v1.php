<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePostCategoryV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_categories', function (Blueprint $table) {
            $table->tinyInteger('show_home_page')->default(0);
            $table->tinyInteger('order_number')->nullable();
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
            $table->dropColumn('show_home_page');
            $table->dropColumn('order_number');
        });
    }
}
