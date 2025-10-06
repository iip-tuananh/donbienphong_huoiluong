<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoCampaignCheckpointProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_campaign_checkpoint_products', function (Blueprint $table) {
            $table->bigIncrements('id');

			$table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id', 'pccp_pcc')->references('id')->on('promo_campaign_checkpoints');

			$table->unsignedBigInteger('campaign_id');
            $table->foreign('campaign_id', 'pccp_pc')->references('id')->on('promo_campaigns');

			$table->unsignedBigInteger('product_id');
            $table->foreign('product_id', 'pccp_p')->references('id')->on('products');

			$table->decimal('qty', 10, 2);

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
        Schema::dropIfExists('promo_campaign_checkpoint_products');
    }
}
