<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPayementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modifiez la table payements
        Schema::table('payements', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère actuelle
            $table->dropForeign(['idFacture']);
            
            // Redéfinir la clé étrangère sans 'onDelete cascade' et avec 'onDelete set null'
            $table->foreign('idFacture')
                  ->references('id')
                  ->on('facture_ventes')
                  ->onDelete('set null') // Mettez la colonne idFacture à null si la facture est supprimée
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revenir à l'ancienne configuration
        Schema::table('payements', function (Blueprint $table) {
            // Supprimer la nouvelle contrainte de clé étrangère
            $table->dropForeign(['idFacture']);
            
            // Ajouter la contrainte avec 'onDelete cascade'
            $table->foreign('idFacture')
                  ->references('id')
                  ->on('facture_ventes')
                  ->onDelete('cascade')
                  ->change();
        });
    }
}
