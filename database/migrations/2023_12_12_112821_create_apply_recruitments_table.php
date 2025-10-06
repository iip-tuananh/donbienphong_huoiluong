<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplyRecruitmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apply_recruitments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('recruitment_id');
            $table->string('fullname');
            $table->string('email');
            $table->string('phone_number');
            $table->string('position_apply');
            $table->text('attachments')->nullable();
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
        Schema::dropIfExists('apply_recruitments');
    }
}
