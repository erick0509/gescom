<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Article;
use App\Models\Depot;
use App\Models\Stock;
use App\Models\Tarif;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;

class ArticlesImport implements ToModel
{
    protected $headerRow = true;

    public function model(array $row)
    {
        if ($this->headerRow) {
            $this->headerRow = false;
            return null; // Ignorer la ligne d'en-tête
        }

        $designation = $row[2]; // La première colonne contient la désignation
        
        $validator = Validator::make(
            ['designation' => $designation],
            [
                'designation' => 'required|max:100'
                
            ]
        );

        if ($validator->fails()) {
            return null;
        }

        // Vérifier si l'article existe
        $article = Article::where('designation', $designation)->first();
        if (!$article) {
            // Si l'article n'existe pas, le créer
            $article = new Article([
                'designation' => $designation,
                // Ajoutez d'autres attributs ici si nécessaire
            ]);
            $article->save();
        }

       $idDepot = Depot::where('intitule', session('depotValue'))->value('id');// Vous pouvez ajuster cela si le nom de la session est différent

        // Vérifier si cet article existe déjà dans le stock pour ce dépôt
        $stock = Stock::where('idDepot', $idDepot)
                        ->where('idArticle', $article->id)
                        ->first();

        if (!$stock) {
            // Si l'article n'existe pas dans ce dépôt, l'ajouter dans Stock
            $stock = new Stock([
                'idDepot' => $idDepot,
                'idArticle' => $article->id,
                'quantiteDepot' => 0,
                'prixMoyenAchat' => 0,
                'prixAchat' => 0
            ]);
            $stock->save();
        }
        $tarifs = Tarif::where('idArticle', $article->id)
                        ->where('idDepot', $idDepot)
                        ->get();
                        

            if ($tarifs->count() < 3) {
                // Si moins de trois tarifs existent, en créer d'autres
                for ($i = $tarifs->count(); $i < 3; $i++) {
                    Tarif::create([
                        'idArticle' => $article->id,
                        'idDepot' => $idDepot,
                        'quantite_min' => null,
                        'quantite_max' => $i === 2 ? 9999999 : null,
                        'prix' => null,
                    ]);
                }
            }
        return $article;
    }
}
