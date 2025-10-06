<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('code')->unique();
            $table->date('bill_date');
            $table->integer('payment_method')->default(1);

            $table->unsignedBigInteger('car_id');
            $table->foreign('car_id')->references('id')->on('cars');
            $table->string('license_plate');
            $table->unsignedBigInteger('vehicle_category_id');
            $table->foreign('vehicle_category_id')->references('id')->on('vehicle_categories');

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->string('customer_name');

            $table->decimal('service_total_cost', 16, 2);
            $table->decimal('product_total_cost', 16, 2);
            $table->decimal('total_cost', 16, 2);
            $table->decimal('sale_cost', 16, 2);
            $table->decimal('cost_after_sale', 16, 2);
            $table->decimal('vat_percent', 5, 2);
            $table->decimal('vat_cost', 16, 2);
            $table->decimal('cost_after_vat', 16, 2);

            $table->integer('status')->default(3);
            $table->text('note')->nullable();

            $table->dateTime('approved_time')->nullable();

            $table->unsignedBigInteger('g7_id');
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
        Schema::dropIfExists('bills');
    }
}
