<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->string('intituleClient'); // Nom du client
            $table->string('contactClient')->nullable(); // Contact du client (nullable si non obligatoire)
            $table->string('adresseClient')->nullable(); // Adresse du client (nullable)
            $table->decimal('solde', 15, 2)->default(0); // Solde du client (default 0)
            $table->timestamps(); // Champs created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
