<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleTransfertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_transferts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idTransfert')->constrained('transferts')->onDelete('cascade'); // Clé étrangère vers la table transferts
            $table->foreignId('idArticle')->constrained('articles')->onDelete('cascade'); // Clé étrangère vers la table articles
            $table->integer('quantite'); // Quantité d'article transférée
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
        Schema::dropIfExists('article_transferts');
    }
}
