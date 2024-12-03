<?php

namespace Database\Factories;

use App\Models\Stock;
use App\Models\Depot;
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Stock::class;
    public function definition()
    {
        $depotId = Depot::pluck('id')->random();
        $articleId = Article::pluck('id')->random();

        return [
            'idDepot' => $depotId,
            'idArticle' => $articleId,
            'quantiteDepot' => $this->faker->numberBetween(1, 100),
        ];
    }
}
