<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColCodeToFixedAssets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->string('code',6)->unique();
            $table->longText('node')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('node');
        });
    }
}
