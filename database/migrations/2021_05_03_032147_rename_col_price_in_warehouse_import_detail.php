<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColPriceInWarehouseImportDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ware_house_import_detail', function (Blueprint $table) {
            $table->renameColumn('price','import_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ware_house_import_detail', function (Blueprint $table) {
            $table->renameColumn('import_price','price');
        });
    }
}
