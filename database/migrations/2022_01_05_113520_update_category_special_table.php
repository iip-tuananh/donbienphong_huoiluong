<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCategorySpecialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_special', function (Blueprint $table) {
            $table->tinyInteger('show_home_page')->default(0);
            $table->tinyInteger('type');
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
        Schema::table('category_special', function (Blueprint $table) {
            $table->dropColumn('show_home_page');
            $table->dropColumn('type');
            $table->dropColumn('order_number');
        });
    }
}
