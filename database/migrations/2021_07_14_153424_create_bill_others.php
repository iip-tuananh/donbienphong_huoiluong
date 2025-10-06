<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillOthers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_others', function (Blueprint $table) {
            $table->bigIncrements('id');

			$table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')->references('id')->on('bills');

            $table->string('name');

            $table->decimal('qty', 10, 2);
            $table->decimal('price', 16, 2);
            $table->decimal('total_cost', 16, 2);

			$table->integer('index');

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
        Schema::dropIfExists('bill_others');
    }
}
