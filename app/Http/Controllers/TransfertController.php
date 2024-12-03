<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use App\Models\Article;
use App\Models\Stock;
use App\Models\Tarif;
use App\Models\Transfert;
use App\Models\ArticleTransfert;
use Illuminate\Support\Facades\Date;
use Illuminate\Http\Request;

class TransfertController extends Controller
{
    public function listeTransfert(Request $request)
    {
        $depotIntitule = session('depotValue');
        $depot = Depot::where('intitule', $depotIntitule)->first();
        
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        $depotId = $depot->id;

        // Récupérer les transferts avec les relations des dépôts source et destination
        $transfertsDepot = Transfert::with(['depotSource', 'depotDestination'])
            ->where('idDepotSource', $depotId)
            ->orWhere('idDepotDestination', $depotId)
            ->orderByDesc('id')
            ->simplePaginate(10);

        $totalTransfert = $transfertsDepot->count();

        return view('doc-transfert')
            ->with('transfertsDepot', $transfertsDepot)
            ->with('totalTransfert', $totalTransfert);
    }


    public function remplirFormulaire()
    {
        // Récupérer l'intitulé du dépôt en session
        $depotValue = session('depotValue');
        
        // Récupérer tous les articles triés par désignation
        $articles = Article::orderBy('designation')->get();

        // Récupérer les dépôts qui ne sont pas égaux à depotValue
        $depots = Depot::where('intitule', '!=', $depotValue)->orderBy('intitule')->get();

        // Retourner la vue avec les articles et les dépôts
        return view('creerTransfert', [
            'articles' => $articles,
            'depots' => $depots
        ]);
    }

    public function creer(Request $request)
    {
        // Décoder les données JSON
        $data = json_decode($request->getContent(), true);
        
        // Récupérer l'intitulé du dépôt source depuis la session
        $depotIntitule = session('depotValue');
        
        // Récupérer le dépôt source par son intitulé
        $depotSource = Depot::where('intitule', $depotIntitule)->first();
        
        // Si le dépôt source n'est pas trouvé, retourner une erreur
       
        // Démarrer une transaction pour garantir l'intégrité des données
        
        try {
            // Initialiser le transfertId à null
            $transfertId = null;

            foreach ($data as $item) {
                // Vérifier s'il s'agit d'un transfert (vérification s'il manque la désignation)
                if (!isset($item['designation'])) {
                    // Récupérer la date du transfert et le dépôt destinataire
                    $dateTransfert = Date::now(); // Utiliser now() pour la date actuelle
                    $depotDestinataire = $item['depotDestinataire'];
                    $commentaire = $item['commentaire'];
                    // Récupérer le dépôt destinataire par son intitulé
                    $depotDestination = Depot::where('intitule', $depotDestinataire)->first();
                    
                    // Si le dépôt destinataire n'est pas trouvé, retourner une erreur
                    if (!$depotDestination) {
                        return response()->json(['error' => 'Dépôt destinataire non trouvé'], 404);
                    }

                    // Créer un nouveau transfert
                    $transfert = Transfert::create([
                        'dateTransfert' => $dateTransfert,
                        'idDepotSource' => $depotSource->id, // ID du dépôt source
                        'idDepotDestination' => $depotDestination->id, // ID du dépôt destinataire
                        'statut' => "en attente",
                        'commentaire'=>$commentaire,
                        'primaryKey' => Transfert::generateCustomPrimaryKey($depotSource->id), // Générer une clé primaire personnalisée
                    ]);

                    // Récupérer l'ID du transfert nouvellement créé
                    $transfertId = $transfert->id;

                } else {
                    // Sinon, il s'agit des articles du transfert
                    $designation = $item['designation'];
                    $article = Article::where('designation', '=', $designation)->first();
                    
                    // Si l'article n'est pas trouvé, retourner une erreur
                    if (!$article) {
                        return response()->json(['error' => 'Article non trouvé : ' . $designation], 404);
                    }

                    // Récupérer la quantité
                    $quantite = $item['quantite'];
                    $quantiteAffichee = $item['quantiteAffichee'];
                    // Créer un enregistrement dans la table ArticleTransfert
                    ArticleTransfert::create([
                        'idTransfert' => $transfertId, // ID du transfert
                        'idArticle' => $article->id,    // ID de l'article
                        'quantite' => $quantite,
                        'quantiteAffichee'=>$quantiteAffichee,
                    ]);


                    
                    // Mettre à jour le stock si nécessaire
                    /*
                    $stock = Stock::where('idArticle', $article->id)
                        ->where('idDepot', $depotSource->id)
                        ->first();

                    if ($stock) {
                        $newQuantity = $stock->quantiteDepot - $quantite;
                        $stock->update(['quantiteDepot' => $newQuantity]);
                    }*/              
                }
            }
            return response()->json(['message' => 'Données enregistrées avec succès'], 200);

        } catch (\Exception $e) {
            // Si une erreur survient, annuler la transaction
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage()], 500);
        }
    }

   
    public function rechercherDocumentTransfert(Request $request)
    {
        // Récupérer la valeur du champ de recherche
        $zoneChercher = $request->input('zoneChercher');
        
        // Récupérer le dépôt en session
        $depotIntitule = session('depotValue');
        $depot = Depot::where('intitule', $depotIntitule)->first();
        
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        $depotId = $depot->id;

        // Récupérer les transferts avec les relations des dépôts source et destination
        $transfertsDepot = Transfert::with(['depotSource', 'depotDestination'])
            ->where('idDepotSource', $depotId)
            ->orWhere('idDepotDestination', $depotId)
        ->where('primaryKey', 'LIKE', '%' . $zoneChercher . '%')
        ->orderBy('primaryKey', 'asc') // Trier les résultats si nécessaire
        ->simplePaginate(10);
        $totalTransfert = $transfertsDepot->count();

        // Retourner la vue avec les résultats
        return view('doc-transfert', compact('transfertsDepot', 'zoneChercher','totalTransfert'));
    }

    public function rechercherParDate(Request $request)
    {
        // Récupérer la date à rechercher
        $zoneChercherDate = $request->input('zoneChercherDate') ?? date('Y-m-d');

        $depotIntitule = session('depotValue');
        $depot = Depot::where('intitule', $depotIntitule)->first();
        
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        $depotId = $depot->id;

        // Récupérer les transferts avec les relations des dépôts source et destination
        $transfertsDepot = Transfert::with(['depotSource', 'depotDestination'])
            ->where('idDepotSource', $depotId)
            ->orWhere('idDepotDestination', $depotId)
        ->whereDate('dateTransfert', $zoneChercherDate)
        ->orderBy('dateTransfert', 'asc') // Trier par date
        ->simplePaginate(10);
        $totalTransfert = $transfertsDepot->count();
        // Retourner la vue avec les résultats
        return view('doc-transfert', compact('transfertsDepot', 'zoneChercherDate','totalTransfert'));
    }

    public function detailsTransfert($id,$page)
    {
        // Récupérer les articles associés à la facture en fonction de son ID
        $transfert = Transfert::findOrFail($id);
        $articlesTransfert = ArticleTransfert::where('idTransfert', $id)->orderBy('id','asc')->get();
        
        // Retourner la vue avec les détails des articles de la facture
        return view('detail-transfert', compact('articlesTransfert','id','transfert','page'));
    }

    public function pageUpdate($id)
    {
        $depotValue = session('depotValue');
        $depotId = Depot::where('intitule', session('depotValue'))->value('id');
        $depots = Depot::where('intitule', '!=', $depotValue)->orderBy('intitule')->get();
        $articles = Article::whereHas('stocks', function ($query) use ($depotId) {
            $query->where('idDepot', $depotId);
        })->orderBy('designation')->get();
        $transfert = Transfert::findOrFail($id);
        $articlesTransfert = ArticleTransfert::where('idTransfert', $id)->orderBy('id','asc')->get();
        return view('transfertModif',['id'=>$id,'articles' => $articles,
        'transfert'=>$transfert,'articleTransfert'=>$articlesTransfert,'depots'=>$depots]);
    }
    public function update(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $depotIntitule = session('depotValue');
        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }
        // Récupérer l'ID du dépôt
        $depotId = $depot->id;
       

        $transfertId=0;
        foreach ($data as $item) {
            
            if (!isset($item['designation'])) {             
                $transfertId = $item['idTransfert'];  
                $transfert = Transfert::findOrFail($transfertId);  
                $articlesTransfert = $transfert->articlesTransferts()->get();
                foreach ($articlesTransfert as $articleTransfert) {
                    $article = Article::findOrFail($articleTransfert->idArticle);
                    /*
                    $stock = Stock::where('idArticle', $articleTransfert->idArticle)
                            ->where('idDepot', $depotId)
                            ->first();
                    $stockQuantiteDepot=$stock->quantiteDepot;
                    if ($stock) {
                        $newQuantity = $stock->quantiteDepot + $articleTransfert->quantite;
                        $stock->update(['quantiteDepot' => $newQuantity]);
                    }*/
                    $articleTransfert->delete();
                }
                
                $transfert->save();
            } 
            else{
                $designation = $item['designation'];
                $quantiteAffichee=$item['quantiteAffichee'];
                $quantite = $item['quantite'];
                $article = Article::where('designation', $designation)->first();  
                
                $articleId=$article->id; 
                ArticleTransfert::create([
                    'idTransfert' => $transfertId,
                    'idArticle' => $articleId,
                    'quantite' => $quantite,
                    'quantiteAffichee'=>$quantiteAffichee,
                ]);
                /*
                $stock = Stock::where('idArticle', $articleId)
                      ->where('idDepot', $depotId)
                      ->first();
                $stockQuantiteDepot=$stock->quantiteDepot;
                if ($stock) {
                    $newQuantity = $stock->quantiteDepot - $quantite;
                    $stock->update(['quantiteDepot' => $newQuantity]);
                }*/
            }
        }
        return response()->json(['message' => 'Données modifiées avec succès',"transfertId"=>$transfertId], 200);
    }

    public function delete($id){
        $depotIntitule = session('depotValue');
        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }
        // Récupérer l'ID du dépôt
        $depotId = $depot->id;
        $transfert = Transfert::findOrFail($id);
        // Récupérer les lignes d'achat associées à la facture
        $articlesTransfert = $transfert->articlesTransferts()->get();

        // Parcourir chaque ligne d'achat pour décrémenter le stock
        foreach ($articlesTransfert as $articleTransfert) {
            $article = Article::findOrFail($articleTransfert->idArticle);

            // Décrémenter la quantité dans le stock correspondant au dépôt
            /*
            $stock = Stock::where('idArticle', $articleTransfert->idArticle)
                      ->where('idDepot', $depotId)
                      ->first();
                      
            $stockQuantiteDepot=$stock->quantiteDepot;
            if ($stock) {
                $newQuantity = $stock->quantiteDepot + $articleTransfert->quantite;
                $stock->update(['quantiteDepot' => $newQuantity]);
            }*/
            // Supprimer la ligne d'achat
            $articleTransfert->delete();
        }
        // Supprimer la facture elle-même
        // Supprimer les articles associés à la facture
        //$facture->articles()->detach();

        // Supprimer la facture elle-même
        
        $transfert->delete();
        return redirect()->route('listeTransfert', ['page' =>1])->with('successDelete', 'Transfert supprimé avec succès.');
    }

    public function confirmerTransfert($id)
    {
        // Récupérer le transfert par ID
        $transfert = Transfert::findOrFail($id);
        
        // Vérifier si le dépôt en session est bien le dépôt destination du transfert
        $depotIntitule = session('depotValue');
        $depotDestination = Depot::where('intitule', $depotIntitule)->first();

        if (!$depotDestination || $depotDestination->id != $transfert->idDepotDestination) {
            return redirect()->route('listeTransfert', ['page' => 1])->with('error', 'Vous n\'êtes pas autorisé à confirmer ce transfert.');
        }

        // Récupérer les articles transférés
        $articlesTransfert = ArticleTransfert::where('idTransfert', $id)->get();

        foreach ($articlesTransfert as $articleTransfert) {
            $article = Article::findOrFail($articleTransfert->idArticle);
            $depotSource = $transfert->idDepotSource;
            $depotDestination = $transfert->idDepotDestination;

            // 1. Gérer le stock du dépôt source (décrémenter)
            $stockSource = Stock::where('idArticle', $article->id)
                                ->where('idDepot', $depotSource)
                                ->first();

            if ($stockSource) {
                // Si l'article existe dans le dépôt source, décrémenter la quantité
                $stockSource->quantiteDepot -= $articleTransfert->quantite;

                // S'assurer que la quantité ne soit pas négative
                if ($stockSource->quantiteDepot < 0) {
                    $stockSource->quantiteDepot = 0;
                }

                $stockSource->save();
            }

            // 2. Gérer le stock du dépôt destination (incrémenter)
            $stockDestination = Stock::where('idArticle', $article->id)
                                    ->where('idDepot', $depotDestination)
                                    ->first();

            if ($stockDestination) {
                // Si l'article existe déjà dans le dépôt destination, additionner la quantité
                $stockDestination->quantiteDepot += $articleTransfert->quantite;
                $stockDestination->save();
            } else {
                // Si l'article n'existe pas encore, le créer dans le stock du dépôt destination
                Stock::create([
                    'idArticle' => $article->id,
                    'idDepot' => $depotDestination,
                    'quantiteDepot' => $articleTransfert->quantite,
                    'prixMoyenAchat' => 0, // Prix moyen d'achat par défaut
                    'prixAchat' => 0, // Prix d'achat par défaut
                ]);
            }

            // 3. Gérer les tarifs (destination)
            $tarifs = Tarif::where('idArticle', $article->id)
                            ->where('idDepot', $depotDestination)
                            ->get();

            if ($tarifs->count() < 3) {
                for ($i = $tarifs->count(); $i < 3; $i++) {
                    Tarif::create([
                        'idArticle' => $article->id,
                        'idDepot' => $depotDestination,
                        'quantite_min' => null,
                        'quantite_max' => $i === 2 ? 9999999 : null,
                        'prix' => null,
                    ]);
                }
            }
        }

        // Mettre à jour le statut du transfert comme 'termine'
        $transfert->statut = 'termine';
        $transfert->save();

        return redirect()->route('listeTransfert', ['page' => 1])->with('successDelete', 'Transfert confirmé avec succès.');
    }

}
