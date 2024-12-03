<?php
use App\Http\Controllers\DepotController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FactureAchatController;
use App\Http\Controllers\FactureVenteController;
use App\Http\Controllers\TransfertController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/',[DepotController::class,"index"])->name("accueil")->middleware('auth');
Route::get('/creerDepot',[DepotController::class,"creer"])->name("creer.depot");
Route::post('/creerDepot',[DepotController::class,"creer"])->name("creer.depot");
Route::delete('/{depot}', [DepotController::class, "delete"])->name("supprimer.depot");
Route::put('/{depot}', [DepotController::class, "update"])->name("modifier.depot");
Route::get('/rechercher-depot', [DepotController::class, 'rechercherDepot'])->name('rechercher.depot')->middleware('auth');

/*--MENU--*/
Route::post('/menu', function (Illuminate\Http\Request $request) {
  
  $depotValue = $request->depotValue;
    // Stocker la valeur en session
    session()->put('depotValue', $depotValue);   
    
    // Faites quelque chose avec $depotValue
    return view('menu')->with('depotValue', $depotValue);
})->name('menu');
/*--ARTICLE--*/
Route::get('/articleParDepot', [ArticleController::class, 'getListeArticlesParDepot'])->name('articleParDepot')->middleware('auth');
Route::get('/article/Creation', [ArticleController::class, 'getListeArticles'])->name('creationArticle')->middleware('auth');
Route::get('/article/creerArticle', [ArticleController::class, 'creer'])->name('creer.article');
Route::post('/article/creerArticle', [ArticleController::class, 'creer'])->name('creer.article');

Route::get('/article/ajouterArticle', [ArticleController::class, 'ajouter'])->name('ajouter.article');
Route::post('/article/ajouterArticle', [ArticleController::class, 'ajouter'])->name('ajouter.article');
Route::put('/articleUpdate/{article}', [ArticleController::class, "update2"])->name("modifier2.article");

Route::delete('/article/{article}', [ArticleController::class, "delete"])->name("supprimer.article");
Route::delete('/detacherArticle/{article}', [ArticleController::class, "detacher"])->name("detacher.article");

Route::put('/article/{article}', [ArticleController::class, "update"])->name("modifier.article");
Route::get('/article/rechercher-article', [ArticleController::class, 'rechercherArticle'])->name('rechercher.article');
Route::get('/article/rechercher-article2', [ArticleController::class, 'rechercherArticle2'])->name('rechercher2.article');

Route::post('/article/import', [ArticleController::class, 'import'])->name('import.article');
Route::get('/get-article-price', [ArticleController::class, 'getArticlePrice']);
Route::get('/get-article-stock', [ArticleController::class, 'getArticleStock']);

//DOCUMENT DE TRANSFERT
Route::get('/rechercher-document-transfert', [TransfertController::class, 'rechercherDocumentTransfert'])->name('rechercher.documenttransfert');
Route::get('/rechercher-document-transfert-par-date', [TransfertController::class, 'rechercherParDate'])->name('rechercherParDate.documenttransfert');

Route::get('/transfertArticle', [TransfertController::class, 'listeTransfert'])->name('listeTransfert')->middleware('auth');
Route::get('/creerTransfert',   [TransfertController::class,'remplirFormulaire'])->name('creationTransfert')->middleware('auth');
Route::post('/transfertArticle/creer', [TransfertController::class, 'creer'])->name('creer.transfertArticle');
Route::get('/transfertArticle/{id}/{page}', [TransfertController::class, 'detailsTransfert'])->name('transfert.details');
Route::post('/transfertArticle/modifierTransfert', [TransfertController::class, 'update'])->name('modifier.tranfert');
Route::get('/transfertArticle/{id}', [TransfertController::class, "pageUpdate"])->name("modifier.documentTransfert");
Route::delete('/transfertArticle/{idTransfert}', [TransfertController::class, "delete"])->name("supprimer.documentTransfert");
Route::post('/transfertArticle/confirmer/{id}', [TransfertController::class, 'confirmerTransfert'])->name('confirmer.transfert');
/*--DOCACHAT--*/
Route::get('/documentAchat', [FactureAchatController::class, 'listeAchatsDepot'])->name('documentachat')->middleware('auth');
Route::get('/documentAchat/{id}/{page}', [FactureAchatController::class, 'detailsFacture'])->name('documentachat.details');
Route::delete('/documentAchat/{idFacture}', [FactureAchatController::class, "delete"])->name("supprimer.documentachat");
Route::get('/facture/{id}/pdf', [FactureAchatController::class, 'generatePDF'])->name('facture.pdf');
Route::get('/documentAchat/rechercher-documentachat', [FactureAchatController::class, 'rechercherDocumentAchat'])->name('rechercher.documentachat');
Route::get('/documentAchat/rechercherParDate-documentachat', [FactureAchatController::class, 'rechercherParDateDocumentAchat'])->name('rechercherParDate.documentachat');
Route::get('/documentAchat/rechercherParStatut-documentachat', [FactureAchatController::class, 'rechercherParStatutDocumentAchat'])->name('rechercherParStatut.documentachat');
Route::get('/factureAchat', [FactureAchatController::class,'remplirFormulaire'])->name('factureAchat')->middleware('auth');
Route::post('/documentAchat/acomptant/{id}/{page}', [FactureAchatController::class,'reglementAcomptantFacture'])->name('reglementAcomptant.factureAchat');
Route::post('/documentAchat/acredit/{id}/{page}', [FactureAchatController::class,'reglementAcreditFacture'])->name('reglementAcredit.factureAchat');
Route::post('/documentAchat/etatPayement', [FactureAchatController::class,'listePayement'])->name('etatPayement.documentachat');

//Route::get('/documentAchat/creerAchat', [FactureAchatController::class, 'creer'])->name('creer.factureAchat');
Route::post('/documentAchat/creerAchat', [FactureAchatController::class, 'creer'])->name('creer.factureAchat');
/*--DOCVENTE--*/

Route::get('/documentVente', [FactureVenteController::class, 'listeVentesDepot'])->name('documentvente')->middleware('auth');
Route::get('/documentVente/{id}/{page}', [FactureVenteController::class, 'detailsFacture'])->name('documentvente.details');
Route::delete('/documentVente/{idFacture}', [FactureVenteController::class, "delete"])->name("supprimer.documentvente");
Route::get('/factureVente/{id}/pdf', [FactureVenteController::class, 'generatePDF'])->name('factureVente.pdf');
Route::get('/factureVente', [FactureVenteController::class,'remplirFormulaire'])->name('factureVente')->middleware('auth');
Route::get('/getPrixUnitaire/{articleId}/{quantite}', [ArticleController::class, 'getPrixUnitaire']);
Route::get('/documentVente/rechercher-documentvente', [FactureVenteController::class, 'rechercherDocumentVente'])->name('rechercher.documentvente');
Route::get('/ventesdepot/nouvellesdonnees', [FactureVenteController::class, 'getNouvellesDonneesVentesDepot'])->name('ventesdepot.nouvellesdonnees');
Route::post('/documentVente/creerVente', [FactureVenteController::class, 'creer'])->name('creer.factureVente');
//CREANCE
Route::get('/getCreances/{id}', [ClientController::class, 'getCreances']);

//vaovao
Route::post('/documentVente/modifierVente', [FactureVenteController::class, 'update'])->name('modifier.factureVente');
//
Route::get('/documentVente/rechercherParDate-documentvente', [FactureVenteController::class, 'rechercherParDateDocumentVente'])->name('rechercherParDate.documentvente');
Route::get('/documentVente/rechercherParStatut-documentvente', [FactureVenteController::class, 'rechercherParStatutDocumentVente'])->name('rechercherParStatut.documentvente');
Route::post('/documentVente/acomptant/{id}/{page}', [FactureVenteController::class,'reglementAcomptantFacture'])->name('reglementAcomptant.facture');
Route::post('/documentVente/acredit/{id}/{page}', [FactureVenteController::class,'reglementAcreditFacture'])->name('reglementAcredit.facture');
Route::post('/documentVente/etatArticleG', [FactureVenteController::class,'listeArticleVendueG'])->name('etatArticle.documentventeG');
Route::post('/documentVente/etatArticle', [FactureVenteController::class,'listeArticleVendue'])->name('etatArticle.documentvente');
Route::post('/documentVente/etatClient', [FactureVenteController::class,'listeFacture'])->name('etatClient.documentvente');
Route::post('/documentVente/etatPayement', [FactureVenteController::class,'listePayement'])->name('etatPayement.documentvente');
//vaovao
Route::get('/documentVente/{id}', [FactureVenteController::class, "pageUpdate"])->name("modifier.documentVente");
/* CAISSE */
Route::delete('/caisse/{idFacture}', [FactureVenteController::class, "deleteCaisse"])->name("supprimer.caisse");
Route::get('/caisse', [FactureVenteController::class, 'listeCommandeAttente'])->name('caisse')->middleware('auth');
Route::get('/caisse/{id}/{page}', [FactureVenteController::class, 'detailsCommande'])->name('commande.details');
Route::post('/caisse/acomptant/{id}/{page}', [FactureVenteController::class,'acomptantFacture'])->name('acomptant.facture');
Route::post('/caisse/confirmer/{id}', [FactureVenteController::class, 'confirmerFacture'])->name('confirmer.facture');
Route::post('/caisse/acredit/{id}/{page}', [FactureVenteController::class,'acreditFacture'])->name('acredit.facture');
Route::get('/caisse/rechercher-documentAttente/', [FactureVenteController::class, 'rechercherDocumentAttente'])->name('rechercher.documentAttente');
Route::put('/caisse/{id}/payerParSolde', [FactureVenteController::class, 'payerSolde'])->name('payerSolde.facture');

//OPERATION CAISSE
Route::get('/operationCaisse', [OperationController::class, 'formOperation'])->name('debitCaisse')->middleware('auth');
Route::post('/operation/store', [OperationController::class, 'store'])->name('operation.store')->middleware('auth');
Route::get('/operation/search', [OperationController::class, 'search'])->name('operation.search');

//FACTURE
/* Authentification */
Route::get('/login',[AuthController::class,"login"])->name("auth.login");
Route::get('/login/setting',[AuthController::class,"parametre"])->name("auth.parametre");
/*vaovao */
Route::get('/codeDepot/setting',[DepotController::class,"parametre"])->name("code-acces-depot.parametre");
Route::post('/codeDepot/setting',[DepotController::class,"updateCode"])->name("code-acces-depot.update");

Route::post('/login/setting',[AuthController::class,"update"])->name("auth.update");
Route::post('/login/reinitialiser',[AuthController::class,"reinitialiser"])->name("auth.reinitialiser");
Route::post('/logout',[AuthController::class,"logout"])->name("auth.logout");
Route::post('/login',[AuthController::class,"doLogin"]);
Route::post('/check-code-acces', [AuthController::class,"checkCodeAcces"])->name('check.code.acces');
//vaovao
Route::post('/check-code-acces-depot', [AuthController::class,"checkCodeAccesDepot"])->name('check.code.acces_depot');

//CLIENT 
Route::get('/clients', [ClientController::class, 'listeClient'])->name('clients.index')->middleware('auth');
Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
Route::post('/client', [ClientController::class, 'store1'])->name('client.store');
Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('supprimer.client');
Route::put('/clients/{client}', [ClientController::class, 'update'])->name('modifier.client');
Route::get('/client/{id}/avancement', [ClientController::class, 'show'])->name('avancement.client');
Route::post('/client/{id}/avancement', [ClientController::class, 'storePayement'])->name('avancement.store');
// Dans le fichier routes/web.php
Route::get('/avancement/{id}/search', [ClientController::class, 'searchPayements'])->name('avancement.search');
Route::get('/avancement/{id}/{page}/detail', [ClientController::class, 'detail'])->name('avancement.detail');
 //FOURNISSEUR 
 Route::get('/fournisseurs', [FournisseurController::class, 'listeFournisseur'])->name('fournisseurs.index')->middleware('auth');
 Route::get('/fournisseurs/search', [FournisseurController::class, 'search'])->name('fournisseurs.search');
 Route::post('/fournisseurs', [FournisseurController::class, 'store'])->name('fournisseurs.store');
  Route::post('/fournisseur', [FournisseurController::class, 'store1'])->name('fournisseur.store');
 Route::delete('/fournisseurs/{fournisseur}', [FournisseurController::class, 'destroy'])->name('supprimer.fournisseur');
Route::put('/fournisseurs/{fournisseur}', [FournisseurController::class, 'update'])->name('modifier.fournisseur');

Route::get('/articles/impression', [ArticleController::class, 'impressionListeArticles'])->name('articles.impression');