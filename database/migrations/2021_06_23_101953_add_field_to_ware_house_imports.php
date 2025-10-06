<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToWareHouseImports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ware_house_imports', function (Blueprint $table) {
            $table->integer('type')->default(1);
			$table->unsignedBigInteger('bill_return_id')->nullable();
			$table->foreign('bill_return_id')->references('id')->on('bill_returns');
			$table->unsignedBigInteger('supplier_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ware_house_imports', function (Blueprint $table) {
            $table->dropColumn('type');
			$table->dropForeign(['bill_return_id']);
			$table->dropColumn('bill_return_id');
        });
    }
}
