<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColWareHouseImportIdToPaySlips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->decimal('amount_after_vat',16,2);
            $table->unsignedBigInteger('ware_house_import_id')->nullable();
            $table->foreign('ware_house_import_id')->references('id')->on('ware_house_imports');
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
            $table->dropForeign(['ware_house_import_id']);
            $table->dropColumn('ware_house_import_id');
            $table->dropColumn('amount_after_vat');
        });
    }
}
