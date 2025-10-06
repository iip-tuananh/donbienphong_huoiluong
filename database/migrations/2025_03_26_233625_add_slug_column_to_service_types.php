<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugColumnToServiceTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->string('slug')->nullable();
            $table->unsignedTinyInteger('type')->nullable()->comment('1: Trải nghiệm, 2: Ưu đãi, 3: Gói nghỉ dưỡng');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('type');
        });
    }
}
