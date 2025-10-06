<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWareHouseImportsTable extends Migration
{
    public function up()
    {
        Schema::create('ware_house_imports', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('code')->unique();
            $table->date('import_date');
            $table->integer('pay_type')->default(1);
            $table->integer('vat_percent');
            $table->decimal('amount',16,2);
            $table->decimal('amount_after_tax',16,2);
            $table->decimal('vat');

            $table->integer('status')->default(0);
            $table->text('note')->nullable();

            $table->unsignedBigInteger('g7_id');
            $table->foreign('g7_id')->references('id')->on('g7_infos');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');

            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('ware_house_imports');
    }
}
