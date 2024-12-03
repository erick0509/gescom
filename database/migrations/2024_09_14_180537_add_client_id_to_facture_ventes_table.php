<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientIdToFactureVentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facture_ventes', function (Blueprint $table) {
            // Ajouter une nouvelle colonne idClient
            $table->unsignedBigInteger('idClient')->after('id')->nullable();

            // Définir la clé étrangère pour idClient
            $table->foreign('idClient')->references('id')->on('clients')->onDelete('set null');

            // Supprimer les colonnes redondantes
            $table->dropColumn('nomClient');
            $table->dropColumn('contactClient');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facture_ventes', function (Blueprint $table) {
            // Supprimer la clé étrangère et la colonne idClient
            $table->dropForeign(['idClient']);
            $table->dropColumn('idClient');

            // Restaurer les colonnes supprimées
            $table->string('nomClient');
            $table->string('contactClient');
        });
    }
}
