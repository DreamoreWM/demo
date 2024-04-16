<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->after('id');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->dateTime('start_time')->after('employee_id');
            $table->dateTime('end_time')->after('start_time');
            $table->unsignedBigInteger('prestation_id')->after('end_time');
            $table->foreign('prestation_id')->references('id')->on('prestations');
            $table->dropForeign(['slot_id']);
            $table->dropColumn('slot_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->unsignedBigInteger('slot_id')->after('id');
            $table->foreign('slot_id')->references('id')->on('slots');
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
            $table->dropForeign(['prestation_id']);
            $table->dropColumn('prestation_id');
        });
    }
};
