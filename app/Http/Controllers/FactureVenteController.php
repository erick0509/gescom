<?php

namespace App\Http\Controllers;
use App\Utils\PdfGenerator;
use App\Models\Depot;
use App\Models\Stock;
use App\Models\Client;
use App\Models\Article;
use App\Models\Tarif;
use Illuminate\Support\Facades\DB;
use App\Models\Payement;
use Illuminate\Http\Request;
use App\Models\FactureVente;
use App\Models\ArticleFactureVente;
use App\Models\Caisse;
use Illuminate\Support\Facades\Date;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class FactureVenteController extends Controller
{
    public function listeVentesDepot(Request $request)
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
        if ($request->has('echeance') && $request->echeance == 'echue') {
            // Filtrer les factures échues (non payées et avec une date d'échéance passée)
            $query = FactureVente::where('idDepot', $depotId);
            $query->where('statut', 'non payee')
                  ->where('dateEcheance', '<', \Carbon\Carbon::now());
            $ventesDepot = $query->simplePaginate(10);
            $totalFacture=$ventesDepot->count();
            // Passer les données à la vue
            return view('docvente', compact('ventesDepot','totalFacture'));
        }
        // Requête pour récupérer la liste des achats du dépôt
        $ventesDepot = FactureVente::where('idDepot', $depotId)->orderByDesc('id');
        $totalFacture=$ventesDepot->count();
        // Passer les ventes du dépôt à la vue
        return view('docvente')->with('ventesDepot', $ventesDepot->simplePaginate(10))->with('totalFacture',$totalFacture);
    }
    public function detailsFacture($id,$page)
    {
        // Récupérer les articles associés à la facture en fonction de son ID
        $facture = FactureVente::findOrFail($id);
        $articlesFacture = ArticleFactureVente::where('idFacture', $id)->orderBy('id', 'asc')->get();
        $sommeTotal = $articlesFacture->sum(function ($article) {
            return ($article->quantite * $article->prixUnitaire);
        });
        $sommeTotal=$sommeTotal-$facture->remise;
        $dateAujourdhui = Carbon::now()->toDateString();
        // Retourner la vue avec les détails des articles de la facture
        return view('details-facture-vente', compact('articlesFacture','id','sommeTotal','facture','page','dateAujourdhui'));
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
        $facture = FactureVente::findOrFail($id);
        // Récupérer les lignes d'achat associées à la facture
        $articlesVente = $facture->articlesVente()->get();

        // Parcourir chaque ligne d'achat pour décrémenter le stock
        foreach ($articlesVente as $articleVente) {
            $article = Article::findOrFail($articleVente->idArticle);

            // Décrémenter la quantité dans le stock correspondant au dépôt
            $stock = Stock::where('idArticle', $articleVente->idArticle)
                      ->where('idDepot', $depotId)
                      ->first();
            $stockQuantiteDepot=$stock->quantiteDepot;
            if ($stock) {
                $newQuantity = $stock->quantiteDepot + $articleVente->quantite;
                $stock->update(['quantiteDepot' => $newQuantity]);
            }
            // Supprimer la ligne d'achat
            $articleVente->delete();
        }
        
        $paiements = Payement::where('idFacture', $id)->where('avancement',1)->get();
        if($paiements){
            foreach ($paiements as $paiement) {
                $paiement->idFacture = null;
                $paiement->dejaUtilise=0;
                $paiement->save();
            }
        }
        $paiements1 = Payement::where('idFacture', $id)->where('avancement',0)->get();
        if($paiements1){
            foreach ($paiements1 as $paiement) {
                $caisse = Caisse::where('idDepot', $depot->id)->first(); // Supposons que 'idDepot' soit la clé étrangère dans la table Caisse

                if ($caisse) {
                    // Incrémenter le solde de la caisse avec le montant du paiement
                    $caisse->montant -= $paiement->somme;
                    $caisse->save(); // Sauvegarder la mise à jour de la caisse
                }
                $paiement->delete();
            }
        }
        
        $facture->delete();
        return redirect()->route('documentvente', ['page' =>1])->with('successDelete', 'Facture de vente supprimé avec succès.');
    }
    public function generatePDF($id)
    {
        // Récupérer les données nécessaires pour générer le PDF
        $facture = FactureVente::findOrFail($id);
        $articlesFacture = ArticleFactureVente::where('idFacture', $id)->get();
        $sommeTotal = $articlesFacture->sum(function ($article) {
            return $article->quantite * $article->prixUnitaire;
        });
        // Générer la vue PDF avec les données
        $pdf = new Dompdf();
        $html = View::make('factureVente_pdf', ['facture' => $facture,'sommeTotal'=>$sommeTotal,
                                                'id'=>$id,'articlesFacture'=>$articlesFacture])->render();
        $pdf->loadHtml($html);
    
        // (Optionnel) Définir des options de configuration de Dompdf
        $pdf->setPaper('A4', 'portrait'); // Format et orientation du papier
        //$pdf->setPaper('A6', 'portrait');
        //$pdf->setPaper([0, 0, 226.77, 567.69], 'portrait');
        // Rendre le PDF
        $pdf->render();
    
        // Renvoyer la réponse avec le contenu PDF pour l'aperçu dans le navigateur
        return $pdf->stream('FV_' . $facture->id . '.pdf', ['Attachment' => 0]);
    }
    public function remplirFormulaire(){
        $depotId = Depot::where('intitule', session('depotValue'))->value('id');
        $articles = Article::whereHas('stocks', function ($query) use ($depotId) {
            $query->where('idDepot', $depotId);
        })->orderBy('designation')->get();
        $clients = Client::where('idDepot', $depotId)->orderBy('intituleClient')->get();
        return view('facventes',['articles' => $articles,'clients'=>$clients]);
    }
    //
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
        $page=0;
        foreach ($data as $item) {
            // Vérifier s'il s'agit d'un article (designation, prix, quantite, montant)
            
            if (!isset($item['designation'])) {
                $dateVente = Date::now();
                $idClient = $item['idClient'];
                $mt=$item['montantTotals'];
                $remiseT=$item['remiseT'];
                $date=$item['dateEcheance'];
                $pkPaiement = $item['pkPaiement'];
                $sommePayee = 0; // Valeur par défaut si aucun paiement en avance

                // Si pkPaiement n'est pas vide, récupérer le paiement
                if (!empty($pkPaiement)) {
                    $paiement = Payement::find($pkPaiement);
                    if ($paiement) {
                        $sommePayee = $paiement->somme;
                    }
                }
                $facture = FactureVente::create([
                    'dateVente' =>$dateVente,
                    'idClient'=>$idClient,
                    'idDepot'=>$depotId,
                    'statut'=>"en attente",
                    'sommePayee'=>$sommePayee,
                    'remise'=>$remiseT,
                    'montantTotal'=>$mt,
                    'dateEcheance'=>$date,
                    'primaryKey'=> FactureVente::generateCustomPrimaryKey($depotId),
                    // Ajoutez d'autres champs selon vos besoins
                ]);
                //     
                $factureId = $facture->id; 
                 // Si un paiement existe, mettre à jour son idFacture
                if (isset($paiement)) {
                    $paiement->idFacture = $factureId;
                    $paiement->dejaUtilise=1;
                    $paiement->save();
                } 
                // Maintenant, vous pouvez utiliser ces variables comme vous en avez besoin dans votre logique de contrôleur.
                // Par exemple, insérer ces données dans la base de données ou les traiter d'une autre manière.
            } 
            // Sinon, il s'agit des données de la facture (date, numeroFacture, nomFournisseur, contactFournisseur)
            else {
                $designation = $item['designation'];
                $quantiteAffichee=$item['quantiteAffichee'];
                $article = Article::where('designation', '=', $designation)->first();  
                $articleId=$article->id;                        
                $prix = $item['prix'];
                $quantite = $item['quantite'];
                $remise = $item['remise'];
                $montantTotal = $quantite * $prix; 
                ArticleFactureVente::create([
                    'idFacture' => $factureId,
                    'idArticle' => $articleId,
                    'quantite' => $quantite,
                    'quantiteAffichee'=>$quantiteAffichee,
                    'prixUnitaire' => $prix,
                    'prixAchat' => $article->stocks->where('idDepot', $depotId)->first()->prixMoyenAchat,
                    'remise' => $remise ,
                ]);
                $stock = Stock::where('idArticle', $articleId)
                      ->where('idDepot', $depotId)
                      ->first();
                $stockQuantiteDepot=$stock->quantiteDepot;
                if ($stock) {
                    $newQuantity = $stock->quantiteDepot - $quantite;
                    $stock->update(['quantiteDepot' => $newQuantity]);
                }
            }
        }
        return response()->json(['message' => 'Données enregistrées avec succès','id'=> $factureId,'page'=>1], 200);
    }

    public function rechercherParDateDocumentVente(Request $request)
    {
        $zoneChercherDate = $request->input('zoneChercherDate');
        $depotValue = session('depotValue');
        $depot = Depot::where('intitule', $depotValue)->first();
        $query = FactureVente::where('idDepot', $depot->id);
        if ($zoneChercherDate) {
            $query->whereDate('created_at', $zoneChercherDate)->orderByDesc('id');;
        }
        $totalFacture = $query->count();
        $ventesDepot = $query->simplePaginate(10)->appends(['zoneChercherDate' => $zoneChercherDate]);

        return view('docvente', compact('zoneChercherDate'))
        ->with('ventesDepot', $ventesDepot)
        ->with('totalFacture', $totalFacture);
    }
    public function rechercherParStatutDocumentVente(Request $request)
    {
        $filterByStatut = $request->input('filterByStatut');
        $depotValue = session('depotValue');
        $depot = Depot::where('intitule', $depotValue)->first();
        $query = FactureVente::where('idDepot', $depot->id)->orderByDesc('id');
        // Filtrer par statut si un statut est sélectionné
        if ($filterByStatut && $filterByStatut !== 'tout') {
            $query->where('statut', $filterByStatut);
        }
        $totalFacture = $query->count();
        $ventesDepot = $query->simplePaginate(10)->appends([
            'filterByStatut' => $filterByStatut, // Ajout de filterBy aux append pour conserver le filtre par statut
        ]);

        return view('docvente', compact('filterByStatut'))
            ->with('ventesDepot', $ventesDepot)
            ->with('totalFacture', $totalFacture);
    }

    public function listeCommandeAttente(Request $request)
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
        $caisse = Caisse::where('idDepot', $depotId)->first();
        if (!$caisse) {
            return redirect()->back()->with('error', 'Caisse non trouvée pour ce dépôt.');
        }
        // Requête pour récupérer la liste des achats du dépôt
        $ventesDepot = FactureVente::where('idDepot', $depotId)->where('statut', 'en attente')->orderBy('id');
        $totalFacture=$ventesDepot->count();
        $dateAujourdhui = Carbon::today();
        $sommePaiements = Payement::join('facture_ventes', 'payements.idFacture', '=', 'facture_ventes.id')
        ->where('facture_ventes.idDepot', $depotId)
        ->whereDate('payements.datePayement', $dateAujourdhui)
        ->sum('payements.somme');
        // Passer les ventes du dépôt à la vue
        return view('caisse')->with('ventesDepot', $ventesDepot->simplePaginate(10))
        ->with('totalFacture',$totalFacture)->with('sommePaiements', $sommePaiements)->with('caisse', $caisse);
    }
    public function detailsCommande($id,$page)
    {
        // Récupérer les articles associés à la facture en fonction de son ID
        $facture = FactureVente::findOrFail($id);
        $articlesFacture = ArticleFactureVente::where('idFacture', $id)->orderBy('id','asc')->get();
        $sommeTotal = $articlesFacture->sum(function ($article) {
            return ($article->quantite * $article->prixUnitaire);
        });
        $sommeTotal=$sommeTotal-$facture->remise;
        $paiementExiste = Payement::where('idFacture', $facture->id)->where('avancement',0)->exists();
        // Retourner la vue avec les détails des articles de la facture
        return view('detail-commande', compact('articlesFacture','id','sommeTotal','facture','page','paiementExiste'));
    }


    public function deleteCaisse($id)
    {
        $depotIntitule = session('depotValue');
        // Récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }
        // Récupérer l'ID du dépôt
        $depotId = $depot->id;
        
        // Récupérer la facture
        $facture = FactureVente::findOrFail($id);       
        // Récupérer les avancements associés à la facture sans les supprimer
        $paiements = Payement::where('idFacture', $id)->where('avancement',1)->get();
        if($paiements){
            foreach ($paiements as $paiement) {
                $paiement->idFacture = null;
                $paiement->dejaUtilise=0;
                $paiement->save();
            }
        }
         // Récupérer les paiements associés à la facture et les supprimer
        $paiements1 = Payement::where('idFacture', $id)->where('avancement',0)->get();
        if($paiements1){
            foreach ($paiements1 as $paiement) {
                $caisse = Caisse::where('idDepot', $depot->id)->first(); // Supposons que 'idDepot' soit la clé étrangère dans la table Caisse

                if ($caisse) {
                    // Incrémenter le solde de la caisse avec le montant du paiement
                    $caisse->montant -= $paiement->somme;
                    $caisse->save(); // Sauvegarder la mise à jour de la caisse
                }
                $paiement->delete();
            }
        }
        

        // Récupérer les lignes d'achat associées à la facture
        $articlesVente = $facture->articlesVente()->get();

        // Parcourir chaque ligne d'achat pour décrémenter le stock
        foreach ($articlesVente as $articleVente) {
            $article = Article::findOrFail($articleVente->idArticle);

            // Décrémenter la quantité dans le stock correspondant au dépôt
            $stock = Stock::where('idArticle', $articleVente->idArticle)
                    ->where('idDepot', $depotId)
                    ->first();
            
            if ($stock) {
                $newQuantity = $stock->quantiteDepot + $articleVente->quantite;
                $stock->update(['quantiteDepot' => $newQuantity]);
            }

            // Supprimer la ligne d'achat
            $articleVente->delete();
        }

        // Mettre à jour les paiements associés pour supprimer la référence à la facture sans les supprimer
        
        // Supprimer la facture elle-même
        $facture->delete();

        return redirect()->route('caisse', ['page' => 1])->with('successDelete', 'Facture de vente supprimée avec succès.');
    }

    public function acomptantFacture(Request $request,$id,$page)
    {
        $facture = FactureVente::findOrFail($id);
        //$facture->statut = 'payee'; // Mettre à jour le statut à "payee"
        $depotIntitule = session('depotValue');
        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }
        // Récupérer l'ID du dépôt
        $depotId = $depot->id;

        $articlesFacture = ArticleFactureVente::where('idFacture', $id)->get();
        $sommeTotal = $articlesFacture->sum(function ($article) {
            return $article->quantite * $article->prixUnitaire;
        });
        $sommeTotal=$sommeTotal-$facture->remise;

        if($facture->sommePayee<$sommeTotal){
            $sm=$facture->sommePayee;
            $facture->sommePayee=$sommeTotal;
            $facture->save();
            $payement = new Payement();
            $payement->primaryKey=Payement::generateCustomPrimaryKey($depotId);
            $payement->datePayement =  Date::now();
            $payement->idClient =$facture->client->id;
            $payement->idFacture = $facture ->id; // ID de la facture à laquelle le paiement est associé

            $payement->somme = $facture->montantTotal-$sm;

            $payement->reste=0;
            $payement->avancement=0;
            $payement->mode=$request->input('modeAc');
            $payement->reference=$request->input('referenceAc');
            $payement->save();
            $depotValue = session('depotValue');
            $depot = Depot::where('intitule', $depotValue)->first();
            // Vérifier si un dépôt est en session
            if ($depotValue) {
                // Trouver la caisse du dépôt en session
                $caisse = Caisse::where('idDepot', $depot->id)->first(); // Supposons que 'idDepot' soit la clé étrangère dans la table Caisse

                if ($caisse) {
                    // Incrémenter le solde de la caisse avec le montant du paiement
                    $caisse->montant += $payement->somme;
                    $caisse->save(); // Sauvegarder la mise à jour de la caisse
                } else {
                    // Gérer l'erreur si la caisse n'est pas trouvée pour le dépôt
                    return back()->with('error', 'Caisse non trouvée pour le dépôt sélectionné.');
                }
            } else {
                return back()->with('error', 'Aucun dépôt sélectionné en session.');
            }
        }     
       return $this->detailsCommande($id, $page);
    }
    public function reglementAcomptantFacture(Request $request,$id,$page)
    {
        $facture = FactureVente::findOrFail($id);
       
        //$facture->statut = 'payee'; // Mettre à jour le statut à "payee"
        $depotIntitule = session('depotValue');
        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }
        // Récupérer l'ID du dépôt
        $depotId = $depot->id;
        $facture->statut = 'payee'; // Mettre à jour le statut à "payee"
        $articlesFacture = ArticleFactureVente::where('idFacture', $id)->get();
        $sommeTotal = $articlesFacture->sum(function ($article) {
            return $article->quantite * $article->prixUnitaire;
        });
        $sommeTotal=$sommeTotal-$facture->remise;
        $payement = new Payement();
        $payement->primaryKey=Payement::generateCustomPrimaryKey($depotId);
        $payement->datePayement =  Date::now();
        $payement->idClient = $facture ->client->id; 
        $payement->idFacture = $facture ->id; // ID de la facture à laquelle le paiement est associé
        $payement->somme = ($sommeTotal-$facture->sommePayee); // Montant du paiement
        $payement->reste=0;
        $payement->avancement=0;
        $facture->sommePayee=$sommeTotal;
        $payement->mode=$request->input('modeAc');
        $payement->reference=$request->input('referenceAc');
        $payement->save();
        $facture->save();

        $depotValue = session('depotValue');
            $depot = Depot::where('intitule', $depotValue)->first();
            // Vérifier si un dépôt est en session
            if ($depotValue) {
                // Trouver la caisse du dépôt en session
                $caisse = Caisse::where('idDepot', $depot->id)->first(); // Supposons que 'idDepot' soit la clé étrangère dans la table Caisse

                if ($caisse) {
                    // Incrémenter le solde de la caisse avec le montant du paiement
                    $caisse->montant += $payement->somme;
                    $caisse->save(); // Sauvegarder la mise à jour de la caisse
                } else {
                    // Gérer l'erreur si la caisse n'est pas trouvée pour le dépôt
                    return back()->with('error', 'Caisse non trouvée pour le dépôt sélectionné.');
                }
            } else {
                return back()->with('error', 'Aucun dépôt sélectionné en session.');
            }
       return $this->detailsFacture($id, $page);
    }
    public function confirmerFacture($id)
    {
        $facture = FactureVente::findOrFail($id);
        $articlesFacture = ArticleFactureVente::where('idFacture', $id)->get();
        $sommeTotal = $articlesFacture->sum(function ($article) {
            return $article->quantite * $article->prixUnitaire;
        });
        $sommeTotal=$sommeTotal-$facture->remise;
        if($sommeTotal>$facture->sommePayee){
            $facture->statut = 'non payee';
        }else{
            $facture->statut = 'payee';
        }
        $facture->save();
        return redirect()->route('caisse', ['page' =>1])->with('successDelete', 'Facture confirmé avec succès.');
    }
    
    public function acreditFacture(Request $request,$id,$page)
    {
        $request->validate([
            'sommePayee' => 'required|numeric|min:0',
        ]);

        //$facture->statut = 'payee'; // Mettre à jour le statut à "payee"
        $depotIntitule = session('depotValue');
        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }
        // Récupérer l'ID du dépôt
        $depotId = $depot->id;

        $facture = FactureVente::findOrFail($id);
         // Mettre à jour le statut à "payee"
        $facture->sommePayee=$request->input('sommePayee');
        $facture->save();
        $payement = new Payement();
        $payement->primaryKey=Payement::generateCustomPrimaryKey($depotId);
        $payement->datePayement =  Date::now();
        $payement->idFacture = $facture ->id; 
        $payement->avancement=0;
        $payement->idClient = $facture ->client->id; // ID de la facture à laquelle le paiement est associé
        $payement->somme = $facture->sommePayee; // Montant du paiement
        $payement->reste=$facture->montantTotal-$facture->sommePayee;
        $payement->mode=$request->input('mode');
        $payement->reference=$request->input('reference');
        $payement->save();
        
        $depotValue = session('depotValue');
            $depot = Depot::where('intitule', $depotValue)->first();
            // Vérifier si un dépôt est en session
            if ($depotValue) {
                // Trouver la caisse du dépôt en session
                $caisse = Caisse::where('idDepot', $depot->id)->first(); // Supposons que 'idDepot' soit la clé étrangère dans la table Caisse

                if ($caisse) {
                    // Incrémenter le solde de la caisse avec le montant du paiement
                    $caisse->montant += $payement->somme;
                    $caisse->save(); // Sauvegarder la mise à jour de la caisse
                } else {
                    // Gérer l'erreur si la caisse n'est pas trouvée pour le dépôt
                    return back()->with('error', 'Caisse non trouvée pour le dépôt sélectionné.');
                }
            } else {
                return back()->with('error', 'Aucun dépôt sélectionné en session.');
            }
        return $this->detailsCommande($id, $page);
    }
    public function reglementAcreditFacture(Request $request,$id,$page)
    {
        $request->validate([
            'sommePayee' => 'required|numeric|min:0',
        ]);
        $facture = FactureVente::findOrFail($id);
         // Mettre à jour le statut à "payee"
        $articlesFacture = ArticleFactureVente::where('idFacture', $id)->get();
        $sommeTotal = $articlesFacture->sum(function ($article) {
            return $article->quantite * $article->prixUnitaire;
        });
        $sommeTotal=$sommeTotal-$facture->remise;
        $depotIntitule = session('depotValue');
        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }
        // Récupérer l'ID du dépôt
        $depotId = $depot->id;
        $payement = new Payement();
        $payement->primaryKey=Payement::generateCustomPrimaryKey($depotId);
        $payement->datePayement =  Date::now();
        $payement->idClient = $facture ->client->id;
        $payement->avancement=0;
        $payement->idFacture = $facture ->id; // ID de la facture à laquelle le paiement est associé
        $payement->somme = $request->input('sommePayee');
        $payement->mode=$request->input('mode');
        $payement->reference=$request->input('reference');
        $facture->sommePayee=$payement->somme+$facture->sommePayee;
        $payement->reste=$facture->montantTotal-$facture->sommePayee;
        if($facture->sommePayee >= $sommeTotal){
            $facture->statut='payee';
        }
        $depotValue = session('depotValue');
            $depot = Depot::where('intitule', $depotValue)->first();
            // Vérifier si un dépôt est en session
            if ($depotValue) {
                // Trouver la caisse du dépôt en session
                $caisse = Caisse::where('idDepot', $depot->id)->first(); // Supposons que 'idDepot' soit la clé étrangère dans la table Caisse

                if ($caisse) {
                    // Incrémenter le solde de la caisse avec le montant du paiement
                    $caisse->montant += $payement->somme;
                    $caisse->save(); // Sauvegarder la mise à jour de la caisse
                } else {
                    // Gérer l'erreur si la caisse n'est pas trouvée pour le dépôt
                    return back()->with('error', 'Caisse non trouvée pour le dépôt sélectionné.');
                }
            } else {
                return back()->with('error', 'Aucun dépôt sélectionné en session.');
            }
        $payement->save();
        $facture->save();
        return $this->detailsFacture($id, $page);
    }
    public function rechercherDocumentVente(Request $request)
    {
       
        $filterBy = $request->input('filterBy');
        $zoneChercher = $request->input('zoneChercher');
        $depotValue = session('depotValue');
        $depot = Depot::where('intitule', $depotValue)->first();
        $query = FactureVente::where('idDepot', $depot->id);

        // Vérifier si la zone de recherche est vide
        if (!empty($zoneChercher)) {
            if ($filterBy === 'id') {
                $query->where('primaryKey','like','%'.$zoneChercher.'%')->orderByDesc('id');
            } elseif ($filterBy === 'client') {
                $query->whereHas('client', function($q) use ($zoneChercher) {
                    $q->where('intituleClient', 'like', '%'.$zoneChercher.'%');
                })->orderByDesc('id');
            }
        }
        $totalFacture = $query->count();
        $ventesDepot = $query->simplePaginate(10)->appends(['filterBy' => $filterBy, 'zoneChercher' => $zoneChercher]);
        return view('docvente',compact('zoneChercher'))->with('ventesDepot', $ventesDepot)
        ->with('totalFacture',$totalFacture)->with('filterBy',$filterBy);
    }
    public function rechercherDocumentAttente(Request $request)
    {
       
        $zoneChercher = $request->input('zoneChercher');
        $depotValue = session('depotValue');
        $depot = Depot::where('intitule', $depotValue)->first();
        $query = FactureVente::where('idDepot', $depot->id)->where('statut', 'en attente');

        // Vérifier si la zone de recherche par primaryKey est fournie
        if (!empty($zoneChercher)) {
            $query->where('primaryKey','like', '%'.$zoneChercher.'%');
        }
        $caisse = Caisse::where('idDepot', $depot->id)->first();
        if (!$caisse) {
            return redirect()->back()->with('error', 'Caisse non trouvée pour ce dépôt.');
        }
        $totalFacture = $query->count();
        $ventesDepot = $query->simplePaginate(10)->appends(['zoneChercher' => $zoneChercher]);
        $dateAujourdhui = Carbon::today();
        $sommePaiements = Payement::join('facture_ventes', 'payements.idFacture', '=', 'facture_ventes.id')
        ->where('facture_ventes.idDepot', $depot->id)
        ->whereDate('payements.datePayement', $dateAujourdhui)
        ->sum('payements.somme');
        return view('caisse', compact('zoneChercher'))
            ->with('ventesDepot', $ventesDepot)
            ->with('totalFacture', $totalFacture)
            ->with('sommePaiements', $sommePaiements)
            ->with('caisse', $caisse);;
    }
    public function listeArticleVendue(Request $request)
    {
        // Valider les dates reçues du formulaire
        $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
        ]);
        $codeAccesValide = filter_var($request->input('codeAccesValide1', false), FILTER_VALIDATE_BOOLEAN);
        
        // Récupérer les dates du formulaire
        $dateDebut = $request->input('dateDebut');
        $dateFin = $request->input('dateFin');

        // Récupérer l'ID du dépôt en fonction de son intitulé
        $depotIntitule = session('depotValue');
        $depot = Depot::where('intitule', $depotIntitule)->first();

        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        // Effectuer la requête pour récupérer les articles vendus entre les dates et pour ce dépôt
        $articlesVendus = ArticleFactureVente::join('articles', 'article_facture_ventes.idArticle', '=', 'articles.id')
            ->whereHas('FactureVente', function ($query) use ($dateDebut, $dateFin, $depot) {
                $query->where('idDepot', $depot->id)
                    ->whereBetween(DB::raw('DATE(created_at)'), [$dateDebut, $dateFin]);
            })
            ->with(['article.stocks' => function ($query) use ($depot) {
                $query->where('idDepot', $depot->id);
            }])
            ->orderBy('articles.designation')
            ->get();

        // Passer les articles vendus à la vue ou effectuer d'autres traitements selon vos besoins
        return view('etat-vente', compact('articlesVendus', 'dateDebut', 'dateFin', 'codeAccesValide'));
    }

    public function listeArticleVendueG(Request $request)
    {
        // Valider les dates reçues du formulaire
        $request->validate([
            'dateDebutG' => 'required|date',
            'dateFinG' => 'required|date|after_or_equal:dateDebut',
        ]);
        $codeAccesValide = filter_var($request->input('codeAccesValide4', false), FILTER_VALIDATE_BOOLEAN);
        
        // Récupérer les dates du formulaire
        $dateDebut = $request->input('dateDebutG');
        $dateFin = $request->input('dateFinG');

        // Récupérer l'ID du dépôt en fonction de son intitulé
        $depotIntitule = session('depotValue');
        $depot = Depot::where('intitule', $depotIntitule)->first();

        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        // Effectuer la requête pour récupérer les articles vendus entre les dates et pour ce dépôt
        $articlesVendus = ArticleFactureVente::join('articles', 'article_facture_ventes.idArticle', '=', 'articles.id')
            ->join('stocks', function ($join) use ($depot) {
                $join->on('stocks.idArticle', '=', 'articles.id')
                    ->where('stocks.idDepot', '=', $depot->id);
            })
            ->whereHas('FactureVente', function ($query) use ($dateDebut, $dateFin, $depot) {
                $query->where('idDepot', $depot->id)
                    ->whereBetween(DB::raw('DATE(created_at)'), [$dateDebut, $dateFin]);
            })
            ->selectRaw('articles.designation, SUM(article_facture_ventes.quantite) as totalQuantite, stocks.quantiteDepot as quantiteRestante')
            ->groupBy('articles.designation', 'stocks.quantiteDepot')
            ->orderBy('articles.designation')
            ->get();


        // Passer les articles vendus à la vue ou effectuer d'autres traitements selon vos besoins
        return view('etat-venteG', compact('articlesVendus', 'dateDebut', 'dateFin', 'codeAccesValide'));
    }
    
    public function listeFacture(Request $request)
    {
        // Valider les dates reçues du formulaire
        $request->validate([
            'dateDebutClient' => 'required|date',
            'dateFinClient' => 'required|date|after_or_equal:dateDebutClient',
        ]);
        $codeAccesValide = filter_var($request->input('codeAccesValide2', false), FILTER_VALIDATE_BOOLEAN);
        // Récupérer les dates du formulaire
        $dateDebut = $request->input('dateDebutClient');
        $dateFin = $request->input('dateFinClient');
        $etat=$request->input('etat');
        $etatText = '';
        if ($etat === '0') {
            $etatText = 'payee';
        } elseif ($etat === '1') {
            $etatText = 'non payee';
        }
        // Récupérer l'ID du dépôt en fonction de son intitulé
        $depotIntitule = session('depotValue');
        $depot = Depot::where('intitule', $depotIntitule)->first();
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        // Effectuer la requête pour récupérer les articles vendus entre les dates et pour ce dépôt
        $factures = FactureVente::where('idDepot', $depot->id)
        ->whereDate('created_at', '>=', $dateDebut)
        ->whereDate('created_at', '<=', $dateFin);
        // Ajouter le filtre pour le statut si $etat est défini
        if (!is_null($etatText)) {
            $factures->where('statut', $etatText);
        } 
        // Trier les factures par date de vente (du plus ancien au plus récent)
        $factures->orderBy('created_at', 'asc');
        // Récupérer les factures
        $factures = $factures->get();
        // Passer les articles vendus à la vue ou effectuer d'autres traitements selon vos besoins
        return view('etat-client', compact('factures','etatText','dateDebut', 'dateFin','codeAccesValide'));
    }
    public function listePayement(Request $request){
        // Valider les dates reçues du formulaire
        $request->validate([
            'dateDebutPayement' => 'required|date',
            'dateFinPayement' => 'required|date|after_or_equal:dateDebutPayement',
        ]);
        $codeAccesValide = filter_var($request->input('codeAccesValide', false), FILTER_VALIDATE_BOOLEAN);
        
        // Récupérer les dates du formulaire
        $dateDebut = $request->input('dateDebutPayement');
        $dateFin = $request->input('dateFinPayement');
        $depotIntitule = session('depotValue');
        
        // Récupérer le dépôt en session
        $depot = Depot::where('intitule', $depotIntitule)->first();
    
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }
    
        // Requête pour récupérer les paiements associés à une facture dans le dépôt
        $payementsAvecFacture = Payement::whereHas('facture', function($query) use ($depot) {
            $query->where('idDepot', $depot->id);               
        })->whereDate('created_at', '>=', $dateDebut)
          ->whereDate('created_at', '<=', $dateFin);
    
        // Requête pour récupérer les paiements sans facture mais associés au client, lui-même lié au dépôt
        $payementsSansFacture = Payement::whereNull('idFacture')
            ->whereHas('client', function($query) use ($depot) {
                // Filtrer les clients par le dépôt en session
                $query->where('idDepot', $depot->id);
            })
            ->whereDate('created_at', '>=', $dateDebut)
            ->whereDate('created_at', '<=', $dateFin);
    
        // Combiner les deux résultats
        $payements = $payementsAvecFacture->union($payementsSansFacture)->orderBy('created_at', 'desc')
        ->get();
    
        // Retourner la vue avec la liste des paiements
        return view('etat-payement', compact('codeAccesValide','payements', 'dateDebut', 'dateFin'));
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
       

        $factureId=0;
        foreach ($data as $item) {
            
            if (!isset($item['designation'])) {             
                $factureId = $item['idFacture'];  
                $facture = FactureVente::findOrFail($factureId);  
                $articlesVente = $facture->articlesVente()->get();
                foreach ($articlesVente as $articleVente) {
                    $article = Article::findOrFail($articleVente->idArticle);

                    // Décrémenter la quantité dans le stock correspondant au dépôt
                    $stock = Stock::where('idArticle', $articleVente->idArticle)
                            ->where('idDepot', $depotId)
                            ->first();
                    $stockQuantiteDepot=$stock->quantiteDepot;
                    if ($stock) {
                        $newQuantity = $stock->quantiteDepot + $articleVente->quantite;
                        $stock->update(['quantiteDepot' => $newQuantity]);
                    }
                    $articleVente->delete();
                }
                $mt=$item['montantTotals'];
                $remiseT=$item['remiseT'];
                $facture->montantTotal=$mt;
                $facture->remise=$remiseT;
                $facture->save();
            } 
            else{
                $designation = $item['designation'];
                $quantiteAffichee=$item['quantiteAffichee'];
                $article = Article::where('designation', $designation)->first();  
                $prix = $item['prix'];
                $quantite = $item['quantite'];
                $remise = $item['remise'];
                $montantTotal = $quantite * $prix; 
                $articleId=$article->id; 
                ArticleFactureVente::create([
                    'idFacture' => $factureId,
                    'idArticle' => $articleId,
                    'quantite' => $quantite,
                    'quantiteAffichee'=>$quantiteAffichee,
                    'prixUnitaire' => $prix,
                    'prixAchat' => $article->stocks->where('idDepot', $depotId)->first()->prixMoyenAchat,
                    'remise' => $remise,
                ]);
                $stock = Stock::where('idArticle', $articleId)
                      ->where('idDepot', $depotId)
                      ->first();
                $stockQuantiteDepot=$stock->quantiteDepot;
                if ($stock) {
                    $newQuantity = $stock->quantiteDepot - $quantite;
                    $stock->update(['quantiteDepot' => $newQuantity]);
                }
            }
        }
        return response()->json(['message' => 'Données modifiées avec succès','id'=> $factureId,'page'=>1], 200);
    }
    public function pageUpdate($id)
    {
        $depotId = Depot::where('intitule', session('depotValue'))->value('id');
        $articles = Article::whereHas('stocks', function ($query) use ($depotId) {
            $query->where('idDepot', $depotId);
        })->orderBy('designation')->get();
        $facture = FactureVente::findOrFail($id);
        $articlesFacture = ArticleFactureVente::where('idFacture', $id)->orderBy('id','asc')->get();
        $payementAvancement = Payement::where('idFacture', $id)
                                  ->where('avancement', 1)
                                  ->first(); 
        return view('facventesModif',['id'=>$id,'articles' => $articles,
        'facture'=>$facture,'articleFacture'=>$articlesFacture,
        'payementAvancement' => $payementAvancement]);
    }

    //PAYER PAR SOLDE
    public function payerSolde(Request $request, $id)
    {
        // Récupérer la facture et le client
        $facture = FactureVente::findOrFail($id);
        $client = $facture->client;

        // Vérifier que le solde du client est suffisant
        if ($client->solde < $request->montant_total) {
            return back()->withErrors(['solde' => 'Le solde du client est insuffisant pour effectuer ce paiement.']);
        }

        // Décrémenter le solde du client
        $client->solde -= $request->montant_total;
        $client->save();

        // Mettre à jour le statut de la facture
        $facture->statut = 'payee';
        $facture->sommePayee=$facture->montantTotal;
        $facture->save();

        // Rediriger avec un message de succès
        return redirect()->route('documentvente')->with('success', 'La facture a été payée et le solde du client a été mis à jour.');
    }
    
}
