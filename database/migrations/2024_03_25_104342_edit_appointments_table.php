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
            $table->dropForeign(['user_id']); // Supprimez cette ligne si la clé étrangère n'a pas été encore ajoutée
            $table->dropColumn('user_id'); // Supprimez cette ligne si la colonne n'a pas été encore ajoutée
            $table->nullableMorphs('bookable'); // 'bookable' est un exemple, vous pouvez choisir le nom qui vous convient
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
