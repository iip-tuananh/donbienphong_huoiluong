<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldInServiceProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_products', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->change();
            $table->renameColumn('quantity', 'qty');
            $table->dropColumn('status');

            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_products', function (Blueprint $table) {
            $table->renameColumn('qty', 'quantity');
            $table->integer('status')->default(1);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }
}
