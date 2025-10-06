<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateG7FixedAssetImportDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g7_fixed_asset_import_details', function (Blueprint $table) {
            $table->bigIncrements('id');

			$table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id', 'gfaid_gfai')->references('id')->on('g7_fixed_asset_imports');

			$table->unsignedBigInteger('asset_id');
            $table->foreign('asset_id', 'gfaid_gfa')->references('id')->on('g7_fixed_assets');
			$table->string('name');

			$table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units');
			$table->string('unit_name');

			$table->decimal('qty', 10, 2);
			$table->decimal('price', 16, 2);
			$table->decimal('total_price', 16, 2);

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
        Schema::dropIfExists('g7_fixed_asset_import_details');
    }
}
