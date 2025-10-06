<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesignDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('design_order_id');
            $table->unsignedTinyInteger('group')->default(1)->comment('1: mặt trước, 2: mặt sau');
            $table->string('type')->nullable()->comment('text, image');
            $table->string('design_text')->nullable();
            $table->string('design_color')->nullable();
            $table->string('design_font')->nullable();
            $table->string('design_font_size')->nullable();
            $table->string('design_font_weight')->nullable();
            $table->string('design_font_style')->nullable();
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
        Schema::dropIfExists('design_details');
    }
}
