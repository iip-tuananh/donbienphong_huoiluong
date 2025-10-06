<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColRecordDateToPaymentVouchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->dateTime('record_date');
            $table->integer('recipient_type_id');
            $table->string('recipient_name')->nullable();
            $table->integer('pay_type')->default(0);
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
            $table->dropColumn('record_date');
            $table->dropColumn('recipient_type_id');
            $table->dropColumn('recipient_name');
            $table->dropColumn('pay_type');
        });
    }
}
