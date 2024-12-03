<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Depot;
use App\Models\Caisse;
use App\Models\Payement;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // Affiche la liste des clients
    public function listeClient()
    {
        $depotIntitule = session('depotValue');

        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();

        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        $depotId = $depot->id;

        // Récupérer les clients du dépôt avec pagination
        $clients = Client::where('idDepot', $depotId)->simplePaginate(10);

        // Calculer le nombre total de clients dans ce dépôt
        $nombreClients = Client::where('idDepot', $depotId)->count();

        // Retourner la vue avec les clients et le nombre total de clients
        return view('client', compact('clients', 'nombreClients'));
    }

    // Fonction de recherche des clients
    public function search(Request $request)
    {
        $zoneChercher = $request->input('zoneChercher');
        $depotIntitule = session('depotValue');
        $depot = Depot::where('intitule', $depotIntitule)->first();
        
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        $depotId = $depot->id;
        // Rechercher des clients par intitulé ou contact
        $clients = Client::where('idDepot',$depotId)->where('intituleClient', 'like', "%$zoneChercher%")
            ->orWhere('contactClient', 'like', "%$zoneChercher%")
            ->simplePaginate(10);
        // Calculer le nombre total de clients dans ce dépôt
        $nombreClients = $clients->count();
        // Retourne la vue avec les résultats de recherche
        return view('client', compact('clients','nombreClients','zoneChercher'));
    }

    public function store(Request $request)
    {
        $depotIntitule = session('depotValue');

        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();

        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        $depotId = $depot->id;

        // Valider les données du formulaire
        $validatedData = $request->validate([
            'intituleClient' => 'required|string|max:255',
            'contactClient' => 'nullable|string|max:255',
            'adresseClient' => 'nullable|string|max:255',
            // Pas de validation pour 'solde', car vous allez le définir vous-même
        ]);

        // Ajouter l'idDepot et solde (avec la valeur par défaut de 0)
        $validatedData['idDepot'] = $depotId;
        $validatedData['solde'] = 0;  // Définir la valeur par défaut pour le solde

        // Créer un nouveau client avec les données validées et l'idDepot
        Client::create($validatedData);

        // Rediriger vers la liste des clients avec un message de succès
        return redirect()->route('clients.index')->with('success', 'Client ajouté avec succès.');
    }
    public function store1(Request $request)
    {
        $depotIntitule = session('depotValue');

        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();

        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        $depotId = $depot->id;

        // Valider les données du formulaire
        $validatedData = $request->validate([
            'intituleClient' => 'required|string|max:255',
            'contactClient' => 'nullable|string|max:255',
            'adresseClient' => 'nullable|string|max:255',
            // Pas de validation pour 'solde', car vous allez le définir vous-même
        ]);

        // Ajouter l'idDepot et solde (avec la valeur par défaut de 0)
        $validatedData['idDepot'] = $depotId;
        $validatedData['solde'] = 0;  // Définir la valeur par défaut pour le solde

        // Créer un nouveau client avec les données validées et l'idDepot
        Client::create($validatedData);

        // Rediriger vers la liste des clients avec un message de succès
        return redirect()->route('factureVente')->with('success', 'Client ajouté avec succès.');
    }

    public function destroy($id)
    {
        // Rechercher le client par son ID
        $client = Client::find($id);

        if (!$client) {
            // Si le client n'existe pas, rediriger avec un message d'erreur
            return redirect()->back()->with('error', 'Client non trouvé.');
        }

        // Supprimer le client
        $client->delete();

        // Rediriger avec un message de succès
        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }
    public function update(Request $request, $id)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'intituleModif' => 'required|string|max:255',
            'contactModif' => 'nullable|string|max:255',
            'adresseModif' => 'nullable|string|max:255',
        ]);

        // Rechercher le client à modifier par son ID
        $client = Client::find($id);

        if (!$client) {
            // Si le client n'existe pas, rediriger avec un message d'erreur
            return redirect()->back()->with('error', 'Client non trouvé.');
        }

        // Mise à jour des informations du client
        $client->update([
            'intituleClient' => $validatedData['intituleModif'],
            'contactClient' => $validatedData['contactModif'],
            'adresseClient' => $validatedData['adresseModif'],
        ]);

        // Rediriger avec un message de succès
        return redirect()->route('clients.index')->with('success', 'Client modifié avec succès.');
    }
    public function show($id)
    {
        $client = Client::findOrFail($id); // Trouver le client par ID
        $paiements = Payement::where('idClient', $id)
                            ->where('avancement', 1) // Ajouter la condition idFacture = null
                            ->simplePaginate(10); 
        return view('avancement', compact('client','paiements')); // Retourner la vue avec les infos du client
    }

    public function getCreances($clientId)
    {
        // Récupérer les paiements du client où idFacture est null
        $paiements = Payement::where('idClient', $clientId)
                            ->whereNull('idFacture')
                            ->where('dejaUtilise', 0)
                            ->where('avancement', 1)
                            ->get();

        // Retourner les paiements sous forme de JSON
        return response()->json(['paiements' => $paiements]);
    }
    public function storePayement(Request $request, $idClient)
    {
        // Validation des données
        $depotIntitule = session('depotValue');
        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }
        // Récupérer l'ID du dépôt
        $depotId = $depot->id;

        $request->validate([
            'somme' => 'required|numeric|min:0',
            'mode_de_payement' => 'required|string',
        ]);

        // Récupération du client pour mettre à jour son solde
        $client = Client::findOrFail($idClient);

        // Mise à jour du solde du client
        $client->solde += $request->somme;
        $client->save();

        // Enregistrement du paiement
        Payement::create([
            'idClient' => $idClient,
            'somme' => $request->somme,
            'mode' => $request->mode_de_payement,
            'primaryKey'=> Payement::generateCustomPrimaryKey($depotId),
            'dejaUtilise'=>0,
            'avancement'=>1,
        ]);

        // Récupération de l'intitulé du dépôt depuis la session
        $depotIntitule = session('depotValue');

        // Vérification si le dépôt est trouvé par son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();
        
        // Si le dépôt n'existe pas, retourner une erreur
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        // Récupération de l'ID du dépôt
        $depotId = $depot->id;

        // Vérification si une caisse associée au dépôt existe
        $caisse = Caisse::where('idDepot', $depotId)->first();
        // Si la caisse n'existe pas, retourner une erreur
        if (!$caisse) {
            return redirect()->back()->with('error', 'Caisse associée au dépôt non trouvée.');
        }

        // Mise à jour du montant en caisse
        $caisse->montant += $request->somme;
        $caisse->save();

        // Redirection avec message de succès
        return redirect()->route('avancement.client', $idClient)->with('success', 'Paiement enregistré avec succès.');
    }
    public function searchPayements(Request $request, $idClient)
    {
        // Validation des dates (facultatif)
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        $client = Client::findOrFail($idClient);
        // Récupérer les dates de début et de fin du formulaire
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $query = Payement::where('idClient', $idClient);
        // Si une date de début est fournie, ajouter une condition pour filtrer à partir de cette date
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        // Si une date de fin est fournie, ajouter une condition pour filtrer jusqu'à cette date
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Récupérer les paiements filtrés
        $paiements = $query->simplePaginate(10);

        // Rediriger vers la vue avec les paiements filtrés
        return view('avancement', compact('paiements','client'));
    }

    public function detail($id,$page)
    {
        $paiement = Payement::findOrFail($id);
        $depotIntitule = session('depotValue');
        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }
        // Logique pour générer le document d'impression, par exemple avec une vue dédiée.
        return view('detail-avancement', compact('paiement','depot','page'));
    }
}

