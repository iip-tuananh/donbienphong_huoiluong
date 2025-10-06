<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateG7FixedAssetImports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g7_fixed_asset_imports', function (Blueprint $table) {
            $table->bigIncrements('id');

			$table->string('code')->unique();
            $table->datetime('import_date');
            $table->decimal('vat_percent', 5, 2);
			$table->decimal('vat_cost', 16, 2);
            $table->decimal('amount',16,2);
            $table->decimal('amount_after_vat',16,2);

            $table->integer('status');
            $table->text('note')->nullable();

			$table->unsignedBigInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers');

            $table->unsignedBigInteger('g7_id');
            $table->foreign('g7_id')->references('id')->on('g7_infos');

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
        Schema::dropIfExists('g7_fixed_asset_imports');
    }
}
