<?php

namespace Database\Factories;

use App\Models\ArticleFactureAchat;
use App\Models\Article;
use App\Models\FactureAchat;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactureAchatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ArticleFactureAchat::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Récupérer les IDs des articles et des factures d'achat existants
        $articleIds = Article::pluck('id')->toArray();
        $factureIds = FactureAchat::pluck('id')->toArray();

        return [
            'idFacture' => $this->faker->randomElement($factureIds),
            'idArticle' => $this->faker->randomElement($articleIds),
            'quantite' => $this->faker->numberBetween(1, 10),
            'prixUnitaire' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}
