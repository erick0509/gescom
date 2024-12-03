<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFournisseurForeignToFactureAchatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facture_achats', function (Blueprint $table) {
            $table->unsignedBigInteger('idFournisseur')->after('idDepot');

            // Définit la clé étrangère avec une contrainte de référence à la table fournisseurs
            $table->foreign('idFournisseur')->references('id')->on('fournisseurs');

            // Supprime les colonnes nomFournisseur et contactFournisseur
            $table->dropColumn('nomFournisseur');
            $table->dropColumn('contactFournisseur');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facture_achats', function (Blueprint $table) {
            $table->dropForeign(['idFournisseur']);
            $table->dropColumn('idFournisseur');

            // Restaure les colonnes nomFournisseur et contactFournisseur
            $table->string('nomFournisseur');
            $table->string('contactFournisseur');
        });
    }
}
