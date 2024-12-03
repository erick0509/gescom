<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdDepotToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            // Ajout de la colonne idDepot qui fait référence à la table depots
            $table->unsignedBigInteger('idDepot')->nullable(); // Nullable si pas obligatoire
            $table->foreign('idDepot')->references('id')->on('depots')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            // Suppression de la clé étrangère et de la colonne idDepot
            $table->dropForeign(['idDepot']);
            $table->dropColumn('idDepot');
        });
    }
}
