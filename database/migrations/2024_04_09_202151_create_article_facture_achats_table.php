<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleFactureAchatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_facture_achats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idArticle');
            $table->unsignedBigInteger('idFacture');
            $table->integer('quantite');
            $table->double('prixUnitaire');
            // Clés étrangères
            $table->foreign('idFacture')->references('id')->on('facture_achats')->onDelete('cascade');
            $table->foreign('idArticle')->references('id')->on('articles')->onDelete('cascade');

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
        Schema::dropIfExists('article_facture_achats');
    }
}
