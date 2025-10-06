<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColExportValueInWarehouseExport extends Migration
{

    public function up()
    {
        Schema::table('warehouse_exports', function (Blueprint $table) {
            $table->decimal('export_value', 16, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('warehouse_exports', function (Blueprint $table) {
            $table->dropColumn('export_value');
        });
    }
}
