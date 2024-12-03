<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FactureAchat;
use App\Models\Depot;
class FactureAchatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = FactureAchat::class;
    public function definition()
    {
        $depotIds = Depot::pluck('id')->all();
        return [
            'dateAchat' => $this->faker->date(),
            'ReferenceFactureAchat' => $this->faker->word,
            'nomFournisseur' => $this->faker->name,
            'contactFournisseur' => $this->faker->phoneNumber,
            'idDepot' => $this->faker->randomElement($depotIds),
        ];
    }
}
