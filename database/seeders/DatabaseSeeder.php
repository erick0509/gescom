<?php

namespace Database\Seeders;
use App\Models\Depot;
use App\Models\Article;
use App\Models\Stock;
use App\Models\FactureAchat;
use App\Models\ArticleFactureAchat;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //Depot::factory(30)->create();
        //Article::factory(100)->create();
        //Stock::factory()->count(20)->create();
        //FactureAchat::factory()->count(100)->create();
        ArticleFactureAchat::factory()->count(100)->create();
        // \App\Models\User::factory(10)->create();
    }
}
