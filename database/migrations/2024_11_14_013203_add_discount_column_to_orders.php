<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountColumnToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('discount_code')->nullable();
            $table->decimal('discount_value', 16, 2)->default(0);
            $table->decimal('total_before_discount', 16, 2)->default(0);
            $table->decimal('total_after_discount', 16, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('discount_code');
            $table->dropColumn('discount_value');
            $table->dropColumn('total_before_discount');
            $table->dropColumn('total_after_discount');
        });
    }
}
