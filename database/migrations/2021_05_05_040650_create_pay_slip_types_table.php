<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaySlipTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_voucher_types', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->unique();
            $table->integer('status')->default(1);
            $table->text('note')->nullable();

            $table->unsignedBigInteger('g7_id')->nullable();
            $table->foreign('g7_id')->references('id')->on('g7_infos');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_voucher_types');
    }
}
