<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFournisseurToPaiementsAchatTableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paiements_achat', function (Blueprint $table) {
            $table->unsignedBigInteger('idFournisseur')->after('idFacture');

            // Crée la clé étrangère pour idFournisseur
            $table->foreign('idFournisseur')->references('id')->on('fournisseurs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paiements_achat', function (Blueprint $table) {
            $table->dropForeign(['idFournisseur']);
            $table->dropColumn('idFournisseur');
        });
    }
}
