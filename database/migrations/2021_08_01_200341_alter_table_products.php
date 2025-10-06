<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('cate_id');
            $table->foreign('cate_id')->references('id')->on('categories');
            $table->unsignedBigInteger('base_price')->nullable();
            $table->longText('body')->nullable();
            $table->longText('intro')->nullable();
            $table->dropColumn('note');
            $table->dropColumn('points');
            $table->dropColumn('unit_name');
            $table->dropColumn('barcode');
            $table->dropColumn('code');
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
            $table->dropForeign(['product_category_id']);
            $table->dropColumn('product_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['cate_id']);
            $table->dropColumn('cate_id');
            $table->dropColumn('base_price');
            $table->dropColumn('body');
            $table->dropColumn('intro');

            $table->unsignedBigInteger('product_category_id');
            $table->foreign('product_category_id')->references('id')->on('product_categories');

            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units');


            $table->string('unit_name');
            $table->string('note');
            $table->string('barcode');
            $table->integer('points');
            $table->string('code');
        });
    }
}
