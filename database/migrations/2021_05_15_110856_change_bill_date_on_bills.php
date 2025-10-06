<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBillDateOnBills extends Migration
{

    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dateTime('bill_date')->change();
        });
    }

    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->date('bill_date')->change();
        });
    }
}
