<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTarifForeignKeyToStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Ajouter une colonne idTarif à la table stocks
            $table->unsignedBigInteger('idTarif')->after('idArticle');

            // Ajouter la clé étrangère qui référence la table tarifs
            $table->foreign('idTarif')->references('id')->on('tarifs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Supprimer la clé étrangère et la colonne associée
            $table->dropForeign(['idTarif']);
            $table->dropColumn('idTarif');
        });
    }
}
