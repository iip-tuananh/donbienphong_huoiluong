<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateG7HasPromoCampaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g7_has_promo_campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');

			$table->unsignedBigInteger('g7_id');
            $table->foreign('g7_id')->references('id')->on('g7_infos');

			$table->unsignedBigInteger('promo_campaign_id');
            $table->foreign('promo_campaign_id', 'ghpc_pc')->references('id')->on('promo_campaigns');

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
        Schema::dropIfExists('g7_has_promo_campaigns');
    }
}
