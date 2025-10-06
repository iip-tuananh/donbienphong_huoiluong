<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColUnitNameToWarehouseImportDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ware_house_import_detail', function (Blueprint $table) {
            $table->string('unit_name');
            $table->dropColumn('unit');
        });
        Schema::table('ware_house_imports', function (Blueprint $table) {
            $table->dropColumn('unit_name');
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
            $table->dropColumn('unit_name');
            $table->string('unit');
        });
        Schema::table('ware_house_imports', function (Blueprint $table) {
            $table->string('unit_name');
        });
    }
}
