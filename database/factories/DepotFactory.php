<?php

namespace Database\Factories;
use App\Models\Depot;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Depot::class;
    public function definition()
    {
        return [
            'intitule' => $this->faker->name,
            'type' => $this->faker->randomElement(['Type 1', 'Type 2', 'Type 3']),
            'adresse' => $this->faker->city
        ];
    }
}
