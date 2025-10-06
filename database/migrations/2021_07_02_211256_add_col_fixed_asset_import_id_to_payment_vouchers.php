<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColFixedAssetImportIdToPaymentVouchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->unsignedBigInteger('g7_fixed_asset_import_id')->nullable();
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
            $table->dropForeign(['g7_fixed_asset_import_id']);
            $table->dropColumn('g7_fixed_asset_import_id');
        });
    }
}
