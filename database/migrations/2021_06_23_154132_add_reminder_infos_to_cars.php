<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReminderInfosToCars extends Migration
{
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->date('registration_deadline')->nullable();
            $table->date('hull_insurance_deadline')->nullable();
            $table->date('maintenance_dateline')->nullable();
            $table->date('insurance_deadline')->nullable();

        });
    }

    public function down()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('registration_deadline');
            $table->dropColumn('hull_insurance_deadline');
            $table->dropColumn('maintenance_dateline');
            $table->dropColumn('insurance_deadline');
        });
    }
}
