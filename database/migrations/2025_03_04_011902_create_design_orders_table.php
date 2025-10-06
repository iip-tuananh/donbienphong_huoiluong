<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesignOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_note')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->decimal('product_price', 15, 2)->default(0);
            $table->integer('product_quantity')->default(0);
            $table->string('product_color')->nullable();
            $table->decimal('product_size_length', 15, 2)->nullable()->comment('Kích thước chiều dài');
            $table->decimal('product_size_width', 15, 2)->nullable()->comment('Kích thước chiều rộng');
            $table->decimal('product_size_height', 15, 2)->nullable()->comment('Kích thước chiều cao');
            $table->text('product_attributes')->nullable()->comment('Thuộc tính của dép');

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
        Schema::dropIfExists('design_orders');
    }
}
