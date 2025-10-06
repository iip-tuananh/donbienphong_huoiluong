<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColInTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receipt_voucher_types', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropForeign(['g7_id']);
            $table->dropColumn('g7_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receipt_voucher_types', function (Blueprint $table) {
            $table->string('code')->nullable();
            $table->unsignedBigInteger('g7_id')->nullable();
            $table->foreign('g7_id')->references('id')->on('g7_infos');
        });
    }
}
