<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCallToConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->boolean('click_call')->default(1);
            $table->boolean('facebook_chat')->default(1);
            $table->boolean('zalo_chat')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->dropColumn('click_call');
            $table->dropColumn('facebook_chat');
            $table->dropColumn('zalo_chat');
        });
    }
}
