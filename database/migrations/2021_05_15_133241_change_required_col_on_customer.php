<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRequiredColOnCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->boolean('gender')->nullable()->change();
            $table->unsignedBigInteger('province_id')->nullable()->change();
            $table->unsignedBigInteger('district_id')->nullable()->change();
            $table->unsignedBigInteger('ward_id')->nullable()->change();
            $table->unsignedBigInteger('customer_group_id')->nullable()->change();
            $table->date('birth_day')->nullable()->change();
            $table->string('adress')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('email')->change();
            $table->boolean('gender')->nullable()->change();
            $table->unsignedBigInteger('province_id')->change();
            $table->unsignedBigInteger('district_id')->change();
            $table->unsignedBigInteger('ward_id')->change();
            $table->unsignedBigInteger('customer_group_id')->change();
            $table->date('birth_day')->change();
            $table->string('adress')->change();
        });
    }
}
