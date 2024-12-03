<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transferts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idDepotSource')->constrained('depots')->onDelete('cascade'); // Clé étrangère pour le dépôt source
            $table->foreignId('idDepotDestination')->constrained('depots')->onDelete('cascade'); // Clé étrangère pour le dépôt destination
            $table->date('dateTransfert'); // Champ pour la date du transfert
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
        Schema::dropIfExists('transferts');
    }
}
