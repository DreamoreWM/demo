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
            // It's important to drop foreign key constraints before dropping the columns.
            $table->dropForeign(['prestation_id']); // This drops the foreign key constraint for prestation_id
            $table->dropForeign(['employee_id']); // This drops the foreign key constraint for employee_id
            
            // Now drop the columns.
            $table->dropColumn('slot_date');
            $table->dropColumn('slot_start_time');
            $table->dropColumn('slot_end_time');
            $table->dropColumn('prestation_id');
            $table->dropColumn('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            //
        });
    }
};
