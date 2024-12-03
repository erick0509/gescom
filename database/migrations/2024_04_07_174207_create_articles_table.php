<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
        public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id(); // Utilisation de l'ID par défaut de Laravel comme clé primaire
            $table->string('designation',100)->unique(); // Champ "designation" avec contrainte unique
            $table->double('prixMoyenAchat'); // Champ "prixMoyenAchat" de type double
            $table->double('prixVente'); // Champ "prixVente" de type double
            $table->timestamps(); // Champs "created_at" et "updated_at" pour la gestion automatique des timestamps
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
