<?php

namespace App\Http\Controllers;
use App\Models\Article;
use App\Models\Stock;
use App\Models\FactureAchat;
use App\Models\Depot;
use App\Models\Tarif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\ArticlesImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;


class ArticleController extends Controller
{
    
    public function getArticleStock(Request $request)
    {
        // Récupérer la désignation depuis le paramètre de requête et décoder les caractères spéciaux
        $designation = urldecode($request->query('designation'));

        // Récupérer la valeur du dépôt depuis la session
        $depotValue = session('depotValue');
        $depot = Depot::where('intitule', $depotValue)->first();

        // Vérifier si le dépôt existe
        if (!$depot) {
            return response()->json(['error' => 'Depot not found'], 404);
        }

        // Rechercher l'article par désignation en s'assurant que la recherche est bien sécurisée
        $article = Article::where('designation', "$designation")->first();

        // Vérifier si l'article existe
        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        // Récupérer le stock pour l'article et le dépôt spécifié
        $stock = Stock::where('idArticle', $article->id)
                    ->where('idDepot', $depot->id)
                    ->first();

        // Vérifier si le stock existe et retourner la quantité disponible
        if ($stock) {
            return response()->json(['quantiteDepot' => $stock->quantiteDepot]);
        } else {
            return response()->json(['quantiteDepot' => 0], 200); // Retourner 0 si le stock n'existe pas
        }
    }
    
    public function getArticlePrice(Request $request)
    {
        // Récupérer l'article en utilisant la désignation
        $designation = urldecode($request->query('designation'));

        $depotValue = session('depotValue');
        $depot = Depot::where('intitule', $depotValue)->first();
        $article = Article::where('designation', $designation)->first();

        // Vérifier si l'article existe
        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        // Récupérer le stock correspondant
        $stock = Stock::where('idArticle', $article->id)->where('idDepot', $depot->id)->first(); 

        // Vérifier si le stock existe et retourner le prix moyen d'achat
        if ($stock) {
            return response()->json(['prixMoyenAchat' => $stock->prixMoyenAchat]);
        } else {
            return response()->json(['prixMoyenAchat' => 0]); // Retourner 0 si le stock n'existe pas
        }
    }

    public function getListeArticlesParDepot(Request $request)
    {
        // Récupérer l'intitulé du dépôt en session
        $depotValue = session('depotValue');
        
        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotValue)->first();
        
        // Si le dépôt n'est pas trouvé, retourner une erreur
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        // Utiliser l'ID du dépôt pour obtenir les articles associés et leurs tarifs spécifiques à ce dépôt
        $articles = Article::whereHas('stocks', function ($query) use ($depot) {
            $query->where('idDepot', $depot->id);
        })->with([
            'stocks' => function ($query) use ($depot) {
                $query->where('idDepot', $depot->id);
            },
            'tarifs' => function ($query) use ($depot) {
                // Filtrer les tarifs par dépôt et trier par ordre croissant de l'ID
                $query->where('idDepot', $depot->id)->orderBy('id', 'asc');
            }
        ])->orderBy('designation');

        // Compter le nombre total d'articles pour ce dépôt
        $totalArticle = $articles->count();

        // Obtenir tous les articles sans filtre de dépôt
        $allArticles = Article::orderBy('designation')->get();

        // Compter le nombre total de tous les articles
        $totalAllArticle = Article::count();
        
        // Récupérer la valeur de codeValide
        $codeValide = $request->query('codeValide', 0);

        // Passer les variables à la vue avec la pagination et en conservant la valeur de codeValide
        return view('article', compact('totalArticle', 'allArticles', 'totalAllArticle', 'codeValide'))
            ->with('articles', $articles->simplePaginate(10)->appends(['codeValide' => $codeValide]));
    }



    public function getListeArticles(Request $request)
    {
        // Récupérer tous les articles sans relation avec depot, stock ou tarif
        $articles = Article::orderBy('created_at', 'desc')->simplePaginate(10); // Pagination à 10 articles par page
        // Compter le nombre total d'articles
        $totalArticle = Article::count();

        // Passer les articles et le total à la vue
        return view('creerArticle')->with('articles', $articles)->with('totalArticle', $totalArticle);
    }

    public function creer(Request $request)
    {
        // Valider la désignation de l'article
        $request->validate([
            "designation" => "required|unique:articles|max:255"
        ]);

        try {
            // Créer l'article avec seulement la désignation
            $article = Article::create([
                'designation' => $request->designation,
                'quantitePack' => $request->quantitePack, // Vous pouvez retirer cette ligne si elle n'est plus nécessaire
                'unite'=> $request->unite
            ]);

            // Retourner un message de succès
            return back()->with('success', 'Article ajouté avec succès!');
        } catch (QueryException $e) {
            // Gérer l'erreur en cas de doublon sur la désignation de l'article
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) { // Code d'erreur pour la contrainte de clé unique
                return back()->with('error', 'Un article avec cette désignation existe déjà!');
            }
            // Gérer les autres erreurs possibles
            return back()->with('error', 'Une erreur est survenue lors de l\'ajout de l\'article.');
        }
    }

    public function ajouter(Request $request)
    {
        $articleId = $request->input('article_id');
    
        // Vérifier si l'ID de l'article est valide
        $article = Article::find($articleId);
        if (!$article) {
            return back()->with('error', 'L\'article avec cette désignation n\'existe pas.');
        }

        // Récupérer l'ID du dépôt correspondant à partir de l'intitulé en session
        $depotId = Depot::where('intitule', session('depotValue'))->value('id');

        if (!$depotId) {
            return back()->with('error', 'Le dépôt spécifié n\'existe pas.');
        }

        try {
            // Créer ou mettre à jour les entrées dans la table Stock
            $stock = Stock::where('idArticle', $article->id)
                        ->where('idDepot', $depotId)
                        ->first();

            if (!$stock) {
                // Créer le stock si il n'existe pas
                Stock::create([
                    'idArticle' => $article->id,
                    'idDepot' => $depotId,
                    'quantiteDepot' => 0, // Quantité par défaut
                    'prixMoyenAchat' => 0, // Prix moyen d'achat par défaut
                    'prixAchat' => 0, // Prix d'achat par défaut
                ]);
            }
            else{
                return back()->with('error', 'L\'article existe deja dans ce Depot.');
            }

            // Créer ou mettre à jour les tarifs associés (optionnel)
            $tarifs = Tarif::where('idArticle', $article->id)
                        ->where('idDepot', $depotId)
                        ->get();
                        

            if ($tarifs->count() < 3) {
                // Si moins de trois tarifs existent, en créer d'autres
                for ($i = $tarifs->count(); $i < 3; $i++) {
                    Tarif::create([
                        'idArticle' => $article->id,
                        'idDepot' => $depotId,
                        'quantite_min' => null,
                        'quantite_max' => $i === 2 ? 9999999 : null,
                        'prix' => null,
                    ]);
                }
            }

            return back()->with('success', 'L\'Article est maintenant disponnible dans cet Depot.');
        } catch (\Exception $e) {
            // Capturer toutes les exceptions pour éviter les erreurs non traitées
            return back()->with('error', 'Une erreur est survenue lors de l\'ajout du stock et des tarifs.');
        }
    }

    public function delete(Article $article)
    {
        // Récupérer le dépôt à partir de la session
        $depotValue = session('depotValue');
        $depotId = Depot::where('intitule', $depotValue)->value('id');
        
        if (!$depotId) {
            return redirect()->route('creationArticle', ['page' => 1])->with('error', 'Dépôt non trouvé.');
        }
        
        // Vérifier s'il existe un enregistrement de l'article dans la table Stock pour ce dépôt
        $stock = Stock::where('idArticle', $article->id)->where('idDepot', $depotId)->first();
        
        if ($stock) {
            return redirect()->route('creationArticle', ['page' => 1])->with('error', 'Impossible de supprimer l\'article car il existe encore un Stock associé à cet Article.');
        }

        // Si aucun stock n'existe, supprimer l'article
        $article->delete();
        
        // Rediriger avec un message de succès
        return redirect()->route('creationArticle', ['page' => 1])->with('successDelete', 'Article supprimé avec succès.');
    }

    public function detacher(Article $article)
    {
        // Récupérer la valeur du dépôt à partir de la session
        $depotValue = session('depotValue');
        
        // Récupérer l'ID du dépôt
        $depotId = Depot::where('intitule', $depotValue)->value('id');
        if (!$depotId) {
            return redirect()->route('article', ['page' => 1])->with('error', 'Dépôt non trouvé.');
        }

        // Détacher toutes les factures associées à l'article
        $article->factures()->detach();

        // Récupérer et supprimer l'enregistrement de Stock correspondant à l'article
        $stock = Stock::where('idArticle', $article->id)->where('idDepot', $depotId)->first();
        if ($stock) {
            $stock->delete();
        } else {
            return redirect()->route('article', ['page' => 1])->with('error', 'Stock non trouvé pour ce dépôt.');
        }

        // Supprimer tous les tarifs associés à l'article et au dépôt spécifié
        Tarif::where('idArticle', $article->id)->where('idDepot', $depotId)->delete();

        return redirect()->route('articleParDepot', ['page' => 1])->with('successDelete', 'Article supprimé avec succès.');
    }

   
    public function update(Request $request, Article $article)
    {
        // Valider les données de la requête
        $request->validate([
            "quantitePackModif" => "nullable|numeric|min:0",
            "unite" => "nullable|string|max:255",
        ], [
            // Messages d'erreur personnalisés si nécessaire
        ]);

        // Mettre à jour uniquement les champs de l'article
        $article->update([
            "quantitePack" => $request->quantitePackModif,
            "unite" => $request->uniteModif,
        ]);

        // Rediriger avec un message de succès
        return back()->with("success", "Votre article est à jour !");
    }

    public function update2(Request $request, Article $article)
    {
        // Récupérer l'ID du dépôt à partir de l'intitulé en session
        $depotId = Depot::where('intitule', session('depotValue'))->value('id');
        
        // Récupérer le stock existant pour cet article et ce dépôt
        $stock = Stock::where('idArticle', $article->id)->where('idDepot', $depotId)->first();
        
        // Validation des données du formulaire
        $request->validate(
            [
                "prixAchatModif" => "required|numeric|min:0",
                "quantiteStockModif" => "required|numeric|min:0",
                "quantitePackModif" => "min:0",
                "quantiteMinModif1" => "min:0",
                "quantiteMaxModif1" => "min:0|gte:quantiteMinModif1|check_intervalles",
                "quantiteMinModif2" => "min:0",
                "quantiteMaxModif2" => "min:0|gte:quantiteMinModif2|check_intervalles",
                "quantiteMinModif3" => "min:0",
                "quantiteMaxModif3" => "min:0|check_intervalles",
            ],
            [
                'quantiteMaxModif1.check_intervalles' => 'Les intervalles de quantités ne doivent pas se chevaucher.',
                'quantiteMaxModif2.check_intervalles' => 'Les intervalles de quantités ne doivent pas se chevaucher.',
                'quantiteMaxModif3.check_intervalles' => 'Les intervalles de quantités ne doivent pas se chevaucher.',
            ]
        );

        // Mettre à jour l'enregistrement du stock s'il existe pour ce dépôt
        if ($stock) {
            $stock->update([
                "prixMoyenAchat" => $request->prixAchatModif,
                "quantiteDepot" => $request->quantiteStockModif,
            ]);
        }

        // Mise à jour des tarifs spécifiques à cet article et ce dépôt
        $tarifs = $article->tarifs()->where('idDepot', $depotId)->orderBy('id', 'asc')->get();
        foreach ($tarifs as $key => $tarif) {
            $tarif->update([
                'prix' => $request->{"prixVenteModif" . ($key + 1)},
                'quantite_min' => $request->{"quantiteMinModif" . ($key + 1)},
                'quantite_max' => $request->{"quantiteMaxModif" . ($key + 1)},
            ]);
        }

        return back()->with("success", "Votre Article et ses tarifs sont à jour pour le dépôt sélectionné !");
    }


    
    public function rechercherArticle(Request $request)
    {
        $zoneChercher = $request->input('zoneChercher');
    
        // Construire la requête de recherche par désignation
        $articles = Article::query();
    
        if (!empty($zoneChercher)) {
            $articles->where('designation', 'LIKE', "%$zoneChercher%");
        }
    
        // Trier par désignation et paginer les résultats
        $articles = $articles->orderBy("designation", "asc");
        $totalArticle = $articles->count();
        $articles = $articles->orderBy('created_at', 'desc')->simplePaginate(10)->appends(['zoneChercher' => $zoneChercher]);
    
        // Retourner la vue avec les articles et les paramètres de recherche
        return view('creerArticle', compact('articles', 'zoneChercher'))->with('totalArticle', $totalArticle);
    }
    
    
   public function rechercherArticle2(Request $request)
{
    $filterBy = $request->input('filterBy');
    $zoneChercher = $request->input('zoneChercher');
    $depotValue = session('depotValue');
    $codeValide = $request->input('codeValide', 0); // Récupère le codeValide

    // Obtenir l'ID du dépôt correspondant à l'intitulé
    $depotId = Depot::where('intitule', $depotValue)->value('id');

    // Construire la requête Eloquent pour les articles
    $articles = Article::whereHas('stocks', function ($query) use ($depotId, $zoneChercher, $filterBy) {
        $query->where('idDepot', $depotId);

        // Appliquer le filtre en fonction de la quantité si spécifié
        if ($filterBy === 'quantiteEgal') {
            $query->where('quantiteDepot', '=', $zoneChercher);
        } elseif ($filterBy === 'quantiteInferieur') {
            $query->where('quantiteDepot', '<', $zoneChercher);
        } elseif ($filterBy === 'quantiteSuperieur') {
            $query->where('quantiteDepot', '>', $zoneChercher);
        }
    })
    ->with([
        'stocks' => function ($query) use ($depotId) {
            $query->where('idDepot', $depotId);
        },
        'tarifs' => function ($query) use ($depotId) {
            // Filtrer les tarifs pour le dépôt sélectionné et trier
            $query->where('idDepot', $depotId)->orderBy('id', 'asc');
        }
    ]);

    // Filtrer par désignation si applicable
    if ($filterBy === 'designation') {
        $articles->where('designation', 'LIKE', "%$zoneChercher%");
    }

    // Trier par désignation
    $articles = $articles->orderBy("designation", "asc");

    // Obtenir le nombre total d'articles filtrés
    $totalArticle = $articles->count();

    // Pagination des résultats avec codeValide
    $articles = $articles->simplePaginate(10)->appends([
        'filterBy' => $filterBy,
        'zoneChercher' => $zoneChercher,
        'codeValide' => $codeValide, // Inclure codeValide dans les liens de pagination
    ]);

    // Tous les articles paginés sans filtres
    $allArticles = Article::orderBy('designation')->simplePaginate(10);

    // Compter le nombre total de tous les articles
    $totalAllArticle = Article::count();

    // Retourner les résultats à la vue
    return view('article', compact('articles', 'totalArticle', 'allArticles', 'totalAllArticle', 'codeValide'));
}


    
    public function getPrixUnitaire($articleId, $quantite)
    {
        // Récupérer l'article et ses tarifs en fonction de l'ID
        $article = Article::findOrFail($articleId);
        
        $depotId = Depot::where('intitule',  session('depotValue'))->value('id');
        $tarifs = Tarif::where('idArticle', $articleId)
                   ->where('idDepot', $depotId)
                   ->orderBy('quantite_min')
                   ->get();
        // Trouver le tarif correspondant à la quantité spécifiée
        $prixUnitaire = null;
        foreach ($tarifs as $tarif) {
            $min = $tarif->quantite_min ?? 0; // Utiliser 0 si quantite_min est NULL
            $max = $tarif->quantite_max ?? 0; // Utiliser PHP_INT_MAX si quantite_max est NULL
            if ($quantite >= $min && $quantite <= $max) {
                $prixUnitaire = $tarif->prix;
                break;
            }
        }
        $quantiteStock = $article->stocks->where('idDepot', $depotId)->first()->quantiteDepot ?? null;
        // Retourner le prix unitaire au format JSON
        return response()->json([
            'prixUnitaire' => $prixUnitaire,
            'quantiteStock' => $quantiteStock,
            'quantitePack' => $article->quantitePack,
            'designation' => $article->designation,
            'unite'=>$article->unite
        ]);
    }
    public function import(Request $request)
    {
        if ($request->hasFile('file')) {
            Excel::import(new ArticlesImport(), request()->file('file'));
            return redirect()->back()->with('success', 'Le fichier CSV a été importé avec succès.');
        } else {
            return redirect()->back()->with('error', 'Aucun fichier n\'a été sélectionné.');
        }
    }
    public function impressionListeArticles(Request $request)
    {
        // Récupérer l'intitulé du dépôt en session
        $depotValue = session('depotValue');

        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotValue)->first();

        // Si le dépôt n'est pas trouvé, retourner une erreur
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        // Récupérer tous les articles pour le dépôt sans pagination
        $articles = Article::whereHas('stocks', function ($query) use ($depot) {
            $query->where('idDepot', $depot->id);
        })->with([
            'stocks' => function ($query) use ($depot) {
                $query->where('idDepot', $depot->id);
            },
            'tarifs' => function ($query) use ($depot) {
                $query->where('idDepot', $depot->id)->orderBy('id', 'asc');
            }
        ])->orderBy('designation')->get();

        $codeValide = $request->query('codeValide', 0);
        // Passer les articles à la vue d'impression
        return view('articles_impression', compact('articles','codeValide'));
    }
}
