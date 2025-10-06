<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColToPaySlips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_voucher_type_id');
            $table->foreign('payment_voucher_type_id')->references('id')->on('payment_voucher_types');

            $table->unsignedBigInteger('fund_account_id')->nullable();
            $table->foreign('fund_account_id')->references('id')->on('fund_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->dropForeign(['payment_voucher_type_id']);
            $table->dropForeign(['fund_account_id']);
            $table->dropColumn('payment_voucher_type_id');
            $table->dropColumn('fund_account_id');
        });
    }
}
