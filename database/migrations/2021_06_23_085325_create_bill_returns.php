<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillReturns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_returns', function (Blueprint $table) {
            $table->bigIncrements('id');

			$table->string('code');
			$table->text('note')->nullable();
			$table->dateTime('bill_date');
			$table->dateTime('approved_time')->nullable();
			$table->integer('status');

			$table->string('license_plate');
			$table->string('customer_name');

			$table->unsignedBigInteger('bill_id')->nullable();
            $table->foreign('bill_id')->references('id')->on('bills');

			$table->unsignedBigInteger('g7_id');
            $table->foreign('g7_id')->references('id')->on('g7_infos');

			$table->decimal('total_cost', 16, 2);
			$table->decimal('vat_percent', 16, 2);
			$table->decimal('vat_cost', 16, 2);
			$table->decimal('cost_after_vat', 16, 2);

			$table->unsignedBigInteger('created_by');
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
        Schema::dropIfExists('bill_returns');
    }
}
