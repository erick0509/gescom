<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdDepotToFournisseursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fournisseurs', function (Blueprint $table) {
            // Ajout de la colonne idDepot
            $table->unsignedBigInteger('idDepot')->nullable(); // Permet de laisser cette colonne vide pour les anciens enregistrements

            // Ajout de la clé étrangère vers la table depots
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
        Schema::table('fournisseurs', function (Blueprint $table) {
             // Supprimer la clé étrangère et la colonne idDepot
             $table->dropForeign(['idDepot']);
             $table->dropColumn('idDepot');
        });
    }
}
