<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMorphPaymentVoucher extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->string('recipientale_type')->nullable();
            $table->unsignedBigInteger('recipientale_id')->nullable();
            $table->string('custom_field')->nullable();
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
            $table->dropColumn('recipientale_type');
            $table->dropColumn('recipientale_id');
            $table->dropColumn('custom_field');
        });
    }
}
