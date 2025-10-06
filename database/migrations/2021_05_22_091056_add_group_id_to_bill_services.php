<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupIdToBillServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_services', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id', 'bs_gsvcg')->references('id')->on('g7_service_vehicle_category_groups');
            $table->dropForeign(['g7_product_id']);
            $table->dropColumn('g7_product_id');
            $table->renameColumn('product_name', 'group_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_services', function (Blueprint $table) {
            $table->dropForeign('bs_gsvcg');
            $table->dropColumn('group_id');
            $table->unsignedBigInteger('g7_product_id')->nullable();
            $table->foreign('g7_product_id')->references('id')->on('g7_products');
            $table->renameColumn('group_name', 'product_name');
        });
    }
}
