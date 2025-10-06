<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoCampaignCheckpoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_campaign_checkpoints', function (Blueprint $table) {
            $table->bigIncrements('id');

			$table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')->references('id')->on('promo_campaigns');

			$table->decimal('from', 16, 2);
			$table->decimal('to', 16, 2);

			$table->integer('type')->nullable();
			$table->decimal('value')->nullable();

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
        Schema::dropIfExists('promo_campaign_checkpoints');
    }
}
