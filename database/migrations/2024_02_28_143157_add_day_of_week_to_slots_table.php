<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('slots', function (Blueprint $table) {
            // Ajouter une colonne pour le nom du jour de la semaine
            $table->string('day_of_week')->after('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('slots', function (Blueprint $table) {
            // S'assurer de retirer la colonne si la migration est annulée
            $table->dropColumn('day_of_week');
        });
    }
};
