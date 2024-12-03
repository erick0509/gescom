<?php

namespace App\Http\Controllers;
use App\Models\FactureAchat;
use App\Models\Depot;
use App\Models\Tarif;
use App\Models\Stock;
use App\Models\ArticleFactureAchat;
use App\Models\Article;
use Dompdf\Dompdf;
use App\Models\PayementAchat;
use App\Models\Payement;
use App\Models\Fournisseur;
use Illuminate\Support\Facades\Date;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FactureAchatController extends Controller
{
    public function listeAchatsDepot(Request $request)
    {
        // Récupérer l'intitulé du dépôt depuis la variable de session
        $depotIntitule = session('depotValue');
        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }
        // Récupérer l'ID du dépôt
        $depotId = $depot->id;
        // Requête pour récupérer la liste des achats du dépôt
        $achatsDepot = FactureAchat::where('idDepot', $depotId)->orderByDesc('id');
        $totalFacture=$achatsDepot->count();
        // Passer les achats du dépôt à la vue
        return view('docachat')->with('achatsDepot', $achatsDepot->simplePaginate(10))->with('totalFacture',$totalFacture);
    }
    public function detailsFacture($id,$page)
    {
        // Récupérer les articles associés à la facture en fonction de son ID
        $facture = FactureAchat::findOrFail($id);
        $articlesFacture = ArticleFactureAchat::where('idFacture', $id)->get();
        $sommeTotal = $articlesFacture->sum(function ($article) {
            return $article->quantite * $article->prixUnitaire;
        });
        // Retourner la vue avec les détails des articles de la facture
        return view('details-facture-achat', compact('articlesFacture','id','sommeTotal','facture','page'));
    }
    public function article()
    {
        return $this->belongsTo(Article::class, 'idArticle');
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
        $facture = FactureAchat::findOrFail($id);
        // Récupérer les lignes d'achat associées à la facture
        $articlesAchat = $facture->articlesAchat()->get();

        // Parcourir chaque ligne d'achat pour décrémenter le stock
        foreach ($articlesAchat as $articleAchat) {
            $article = Article::findOrFail($articleAchat->idArticle);

            // Décrémenter la quantité dans le stock correspondant au dépôt
            $stock = Stock::where('idArticle', $articleAchat->idArticle)
                      ->where('idDepot', $depotId)
                      ->first();
            $stockQuantiteDepot=$stock->quantiteDepot;
            if ($stock) {
                $newQuantity = $stock->quantiteDepot - $articleAchat->quantite;
                $stock->update(['quantiteDepot' => $newQuantity]);
            }
            if($stock->quantiteDepot!=0){
                $stock->prixMoyenAchat = (($stock->prixMoyenAchat * $stockQuantiteDepot) -
                ($articleAchat->quantite * $articleAchat->prixUnitaire)) / 
                $stock->quantiteDepot;
                $stock->prixMoyenAchat = round($stock->prixMoyenAchat, 2);
                $stock->save();
            }
            else{
                $stock->prixMoyenAchat =0;
                $stock->prixAchat=0;
                $stock->save();
            }
            // Supprimer la ligne d'achat
            $articleAchat->delete();
        }
        // Supprimer la facture elle-même
        // Supprimer les articles associés à la facture
        //$facture->articles()->detach();
        $paiements = PayementAchat::where('idFacture', $facture->id)->get();
        foreach ($paiements as $paiement) {
            $paiement->delete();
        }
        // Supprimer la facture elle-même
        $facture->delete();
        return redirect()->route('documentachat', ['page' =>1])->with('successDelete', 'Facture d\'achat supprimé avec succès.');
    }
    public function generatePDF($id)
    {
        // Récupérer les données nécessaires pour générer le PDF
        $facture = FactureAchat::findOrFail($id);
        $articlesFacture = ArticleFactureAchat::where('idFacture', $id)->get();
        $sommeTotal = $articlesFacture->sum(function ($article) {
            return $article->quantite * $article->prixUnitaire;
        });
        // Générer la vue PDF avec les données
        $pdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $pdf->setOptions($options);
        $pdf->setPaper('80mm', 'auto');
        $pdf->loadHtml(view('facture_pdf', compact('facture', 'articlesFacture', 'sommeTotal','id')));

        // Générer le PDF
        $pdf->render();

        // Télécharger le PDF
         return $pdf->stream('FA_'.$id.'.pdf');
    }
    public function rechercherDocumentAchat(Request $request)
    {
       
        $filterBy = $request->input('filterBy');
        $zoneChercher = $request->input('zoneChercher');
        $depotValue = session('depotValue');
        $depot = Depot::where('intitule', $depotValue)->first();
        $query = FactureAchat::where('idDepot', $depot->id)->orderBydesc('id');

        // Vérifier si la zone de recherche est vide
        if (!empty($zoneChercher)) {
            if ($filterBy === 'id') {
                $query->where('primaryKey','like', '%' . $zoneChercher . '%');
            } elseif ($filterBy === 'reference') {
            $query->where('ReferenceFactureAchat', 'like', '%' . $zoneChercher . '%');
            } elseif ($filterBy === 'fournisseur') {
                $query->whereHas('fournisseur', function($q) use ($zoneChercher) {
                    $q->where('intitule', 'like', '%' . $zoneChercher . '%');
                });
            }
        }
        $totalFacture = $query->count();
        $achatsDepot = $query->simplePaginate(10)->appends(['filterBy' => $filterBy, 'zoneChercher' => $zoneChercher]);
        return view('docachat',compact('zoneChercher'))->with('achatsDepot', $achatsDepot)
        ->with('totalFacture',$totalFacture)->with('filterBy',$filterBy);
    }
    public function rechercherParDateDocumentAchat(Request $request)
    {
        $zoneChercherDate = $request->input('zoneChercherDate');
        $depotValue = session('depotValue');
        $depot = Depot::where('intitule', $depotValue)->first();
        $query = FactureAchat::where('idDepot', $depot->id);
        if ($zoneChercherDate) {
            $query->whereDate('dateAchat', $zoneChercherDate);
        }
        $totalFacture = $query->count();
        $achatsDepot = $query->simplePaginate(10)->appends(['zoneChercherDate' => $zoneChercherDate]);

        return view('docachat', compact('zoneChercherDate'))
        ->with('achatsDepot', $achatsDepot)
        ->with('totalFacture', $totalFacture);
    }
    public function remplirFormulaire(){
        $depotId = Depot::where('intitule', session('depotValue'))->value('id');
        $articles=Article::orderBy('designation')->get();
        $fournisseurs = Fournisseur::where('idDepot', $depotId)->orderBy('intitule')->get();
        return view('facachats',['articles' => $articles,'fournisseurs'=>$fournisseurs]);
    }
    
    public function creer(Request $request)
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
        $factureId=0;
    
        foreach ($data as $item) {
            // Vérifier s'il s'agit d'un article (designation, prix, quantite, montant)
            if (!isset($item['designation'])) {
                $date = $item['dateAchat'];
                $numeroFacture = $item['numeroFacture'];
                $idFournisseur = $item['idFournisseur'];
                $statut=$item['statut'];
                $sommePayee=$item['sommePayee'];
                $mt=$item['montantTotals'];
                $mode=$item['mode'];
                $reference=$item['reference'];
                $facture = FactureAchat::create([
                    'ReferenceFactureAchat' => $numeroFacture ,
                    'idFournisseur' => $idFournisseur ,
                    'dateAchat' => $date,
                    'idDepot'=>$depotId,
                    'statut'=> $statut,
                    'sommePayee'=>$sommePayee,
                    'montantTotal'=>$mt,
                    'primaryKey'=> FactureAchat::generateCustomPrimaryKey($depotId),
                    // Ajoutez d'autres champs selon vos besoins
                ]);    
                //     
                $factureId = $facture->id;   
                $payement = new PayementAchat();
                $payement->datePayement = Date :: now();
                $payement->idFournisseur=$idFournisseur;
                $payement->idFacture = $facture->id; // ID de la facture à laquelle le paiement est associé
                $payement->somme = $facture->sommePayee; // Montant du paiement
                $payement->reste=$mt-$payement->somme;
                $payement->mode=$mode;
                $payement->reference=$reference;
                $payement->save();
                // Maintenant, vous pouvez utiliser ces variables comme vous en avez besoin dans votre logique de contrôleur.
                // Par exemple, insérer ces données dans la base de données ou les traiter d'une autre manière.
            } 
            // Sinon, il s'agit des données de la facture (date, numeroFacture, nomFournisseur, contactFournisseur)
            else {
                $designation = $item['designation'];
                $article = Article::where('designation', '=', $designation)->first();  
                $articleId=$article->id;        
                
                $prix = $item['prix'];
                $quantite = $item['quantite'];
                $montantTotal = $quantite * $prix; 

                ArticleFactureAchat::create([
                    'idFacture' => $factureId,
                    'idArticle' => $articleId,
                    'quantite' => $quantite,
                    'prixUnitaire' => $prix,
                ]);

                $stock = Stock::where('idDepot', $depotId)->where('idArticle', $articleId)->first();
                if ($stock) {
                    $stockQuantiteDepot=$stock->quantiteDepot;
                    $stock->quantiteDepot += $quantite;
                    //$stock->prixMoyenAchat = ($stock->prixMoyenAchat * $stockQuantiteDepot + $montantTotal) / ($stock ->quantiteDepot);
                    $stock->prixMoyenAchat=$prix;
                    $stock->prixAchat = 0;
                    $stock->prixMoyenAchat = round($stock->prixMoyenAchat, 2);
                    $stock->save();
                } else {
                    // Créer une nouvelle entrée dans le stock si aucune entrée correspondante n'est trouvée
                    Stock::create([
                        'idDepot' => $depotId,
                        'idArticle' => $articleId,
                        'quantiteDepot' => $quantite,
                        'prixMoyenAchat' => $prix,
                        'prixAchat' => 0,
                    ]);
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
                }
                
            }
        }
        return response()->json(['message' => 'Données enregistrées avec succès'], 200);
    }
    public function reglementAcomptantFacture(Request $request,$id,$page)
    {
        $facture = FactureAchat::findOrFail($id);
        $facture->statut = 'payee'; // Mettre à jour le statut à "payee"
        $articlesFacture = ArticleFactureAchat::where('idFacture', $id)->get();
        $sommeTotal = $articlesFacture->sum(function ($article) {
            return $article->quantite * $article->prixUnitaire;
        });
        
        $payement = new PayementAchat();
        $payement->datePayement =  Date::now();
        $payement->idFacture = $facture ->id; // ID de la facture à laquelle le paiement est associé
        $payement->somme = ($sommeTotal-$facture->sommePayee); // Montant du paiement
        $payement->reste=0;
        $facture->sommePayee=$sommeTotal;
        $payement->mode=$request->input('modeAc');
        $payement->reference=$request->input('referenceAc');
        $payement->save();
        $facture->save();
       return $this->detailsFacture($id, $page);
    }
    public function reglementAcreditFacture(Request $request,$id,$page)
    {
        $request->validate([
            'sommePayee' => 'required|numeric|min:0',
        ]);
        $facture = FactureAchat::findOrFail($id);
         // Mettre à jour le statut à "payee"
        $articlesFacture = ArticleFactureAchat::where('idFacture', $id)->get();
        $sommeTotal = $articlesFacture->sum(function ($article) {
            return $article->quantite * $article->prixUnitaire;
        });
        $facture->sommePayee=$request->input('sommePayee')+$facture->sommePayee;
        if($facture->sommePayee >= $sommeTotal){
            $facture->statut='payee';
        }
        $payement = new PayementAchat();
        $payement->datePayement =  Date::now();
        $payement->idFacture = $facture ->id; // ID de la facture à laquelle le paiement est associé
        $payement->somme = $request->input('sommePayee');
        $payement->reste=$facture->montantTotal-$facture->sommePayee;
        $payement->mode=$request->input('mode');
        $payement->reference=$request->input('reference');
        $payement->save();
        $facture->save();
        return $this->detailsFacture($id, $page);
    }
    public function rechercherParStatutDocumentAchat(Request $request)
    {
        $filterByStatut = $request->input('filterByStatut');
        $depotValue = session('depotValue');
        $depot = Depot::where('intitule', $depotValue)->first();
        $query = FactureAchat::where('idDepot', $depot->id)->orderByDesc('id');
        // Filtrer par statut si un statut est sélectionné
        if ($filterByStatut && $filterByStatut !== 'tout') {
            $query->where('statut', $filterByStatut);
        }
        $totalFacture = $query->count();
        $achatsDepot = $query->simplePaginate(10)->appends([
            'filterByStatut' => $filterByStatut, // Ajout de filterBy aux append pour conserver le filtre par statut
        ]);

        return view('docachat', compact('filterByStatut'))
            ->with('achatsDepot', $achatsDepot)
            ->with('totalFacture', $totalFacture);
    }
    public function listePayement(Request $request){
        // Valider les dates reçues du formulaire
        $request->validate([
            'dateDebutPayement' => 'required|date',
            'dateFinPayement' => 'required|date|after_or_equal:dateDebutPayement',
        ]);

        // Récupérer les dates du formulaire
        $dateDebut = $request->input('dateDebutPayement');
        $dateFin = $request->input('dateFinPayement');
        $depotIntitule = session('depotValue');
        $depot = Depot::where('intitule', $depotIntitule)->first();

        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }
        $payements = PayementAchat::whereHas('facture', function($query) use ($depot) {
            $query->where('idDepot', $depot->id);               
        })->whereDate('datePayement', '>=', $dateDebut)
        ->whereDate('datePayement', '<=', $dateFin)->get();
    
        // Retourner la vue avec la liste des paiements
        return view('etat-payement-achat',compact('payements','dateDebut','dateFin'));
    }
}

