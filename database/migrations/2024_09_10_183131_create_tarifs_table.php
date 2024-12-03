<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarifsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarifs', function (Blueprint $table) {
            $table->id();
            
            // Clé étrangère vers la table articles
            $table->foreignId('idArticle')->constrained('articles')->onDelete('cascade');
            
            // Clé étrangère vers la table depots
            $table->foreignId('idDepot')->constrained('depots')->onDelete('cascade');
            
            // Autres colonnes
            $table->integer('quantite_min')->nullable();
            $table->integer('quantite_max')->nullable();
            $table->decimal('prix', 8, 2)->nullable();

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
        Schema::dropIfExists('tarifs');
    }
}
