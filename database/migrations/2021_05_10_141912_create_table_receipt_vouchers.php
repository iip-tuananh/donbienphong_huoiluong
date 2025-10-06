<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableReceiptVouchers extends Migration
{
    public function up()
    {
        Schema::create('receipt_voucher_types', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('code')->unique();
            $table->string('name');

            $table->integer('status')->default(1);
            $table->text('note')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');

            $table->unsignedBigInteger('g7_id');
            $table->foreign('g7_id')->references('id')->on('g7_infos');

            $table->timestamps();
        });

        Schema::create('receipt_vouchers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('code')->unique();
            $table->decimal('value',16,2);
            $table->dateTime('record_date');
            $table->integer('pay_type');
            $table->integer('payer_type_id');
            $table->integer('status')->default(1);
            $table->text('note')->nullable();

            $table->unsignedBigInteger('receipt_voucher_type_id');
            $table->foreign('receipt_voucher_type_id')->references('id')->on('receipt_voucher_types');

            $table->unsignedBigInteger('bill_id')->nullable();
            $table->foreign('bill_id')->references('id')->on('bills');

            $table->string('payer_type')->nullable();
            $table->unsignedBigInteger('payer_id')->nullable();
            $table->string('payer_name')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');

            $table->unsignedBigInteger('g7_id');
            $table->foreign('g7_id')->references('id')->on('g7_infos');

            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('receipt_voucher_types');
        Schema::dropIfExists('receipt_vouchers');
    }
}
