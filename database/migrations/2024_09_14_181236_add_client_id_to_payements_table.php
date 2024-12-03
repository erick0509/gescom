<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientIdToPayementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payements', function (Blueprint $table) {
            // Ajouter la colonne idClient
            $table->unsignedBigInteger('idClient')->nullable()->after('id');

            // Définir la clé étrangère pour idClient
            $table->foreign('idClient')->references('id')->on('clients')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payements', function (Blueprint $table) {
            // Supprimer la clé étrangère et la colonne idClient
            $table->dropForeign(['idClient']);
            $table->dropColumn('idClient');
        });
    }
}
