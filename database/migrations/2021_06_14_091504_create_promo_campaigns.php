<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoCampaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');

			$table->string('name');
			$table->string('code');
			$table->integer('type');
			$table->integer('status')->default(1);

			$table->integer('limit')->nullable();
			$table->integer('used')->default(0);

			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();

			$table->text('note')->nullable();

			$table->boolean('for_all')->default(0);

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
        Schema::dropIfExists('promo_campaigns');
    }
}
