<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromoToBills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->unsignedBigInteger('promo_id')->nullable();
            $table->foreign('promo_id')->references('id')->on('promo_campaigns');
			$table->decimal('promo_value', 16, 2)->default(0);
			$table->decimal('cost_after_promo', 16, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['promo_id']);
			$table->dropColumn('promo_id');
			$table->dropColumn('promo_value');
			$table->dropColumn('cost_after_promo');
        });
    }
}
