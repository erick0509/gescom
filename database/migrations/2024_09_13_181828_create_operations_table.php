<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idDepot')->constrained('depots')->onDelete('cascade'); // Clé étrangère vers la table Depot
            $table->string('type'); // Type d'opération
            $table->date('date_operation'); // Date de l'opération
            $table->decimal('montant', 15, 2); // Montant de l'opération
            $table->string('commentaire');
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
        Schema::dropIfExists('operations');
    }
}

