<?php

namespace Database\Factories;
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Article::class;
    public function definition()
    {
        return [
            'designation' => $this->faker->unique()->word,
            'prixMoyenAchat' => $this->faker->randomFloat(2, 10, 100),
            'prixVente' => $this->faker->randomFloat(2, 50, 200),
        ];
    }
}
