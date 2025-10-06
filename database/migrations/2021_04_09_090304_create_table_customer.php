<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCustomer extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('mobile');
            $table->string('email')->unique();
            $table->boolean('gender');
            $table->date('birth_day');
            $table->string('adress');
            $table->integer('status')->default(1);

            $table->unsignedBigInteger('customer_group_id');
            $table->foreign('customer_group_id')->references('id')->on('customer_groups');

            $table->unsignedBigInteger('province_id');
            $table->foreign('province_id')->references('id')->on('provinces');

            $table->unsignedBigInteger('district_id');
            $table->foreign('district_id')->references('id')->on('districts');

            $table->unsignedBigInteger('ward_id');
            $table->foreign('ward_id')->references('id')->on('wards');

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
        Schema::dropIfExists('customers');
    }
}
