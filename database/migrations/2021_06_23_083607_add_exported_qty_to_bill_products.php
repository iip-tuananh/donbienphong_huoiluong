<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExportedQtyToBillProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_products', function (Blueprint $table) {
            $table->decimal('exported_qty', 10, 2)->default(0);
			$table->decimal('returned_qty', 10, 2)->default(0);
			$table->decimal('export_price', 16, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_products', function (Blueprint $table) {
            $table->dropColumn('exported_qty');
			$table->dropColumn('returned_qty');
			$table->dropColumn('export_price');
        });
    }
}
