<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFactureVentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facture_ventes', function (Blueprint $table) {
            $table->id();
            $table->date('dateVente');
            $table->string('nomClient');
            $table->string('contactClient');
            $table->string('statut');
            $table->unsignedBigInteger('idDepot'); // Ajoute la colonne idDepot
            $table->foreign('idDepot')->references('id')->on('depots'); // Définit la contrainte de clé étrangère
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facture_ventes');
    }
}
