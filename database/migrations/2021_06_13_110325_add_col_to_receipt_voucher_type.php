<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColToReceiptVoucherType extends Migration
{

    public function up()
    {
        Schema::table('receipt_voucher_types', function (Blueprint $table) {
            $table->boolean('accounting')->default(0);
        });
        Schema::table('payment_voucher_types', function (Blueprint $table) {
            $table->boolean('accounting')->default(0);
        });
    }

    public function down()
    {
        Schema::table('receipt_voucher_types', function (Blueprint $table) {
            $table->dropColumn('accounting');
        });
        Schema::table('payment_voucher_types', function (Blueprint $table) {
            $table->dropColumn('accounting');
        });
    }
}
