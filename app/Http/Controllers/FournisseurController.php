<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fournisseur;
use App\Models\Depot;

class FournisseurController extends Controller
{
    public function listeFournisseur()
    {
        $depotIntitule = session('depotValue');

        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();

        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        $depotId = $depot->id;

        // Récupérer les clients du dépôt avec pagination
        $fournisseurs = Fournisseur::where('idDepot', $depotId)->simplePaginate(10);

        // Calculer le nombre total de clients dans ce dépôt
        $nombreFournisseurs = Fournisseur::where('idDepot', $depotId)->count();

        // Retourner la vue avec les clients et le nombre total de clients
        return view('fournisseur', compact('fournisseurs', 'nombreFournisseurs'));
    }

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
        $fournisseurs = Fournisseur::where('idDepot',$depotId)->where('intitule', 'like', "%$zoneChercher%")
            ->orWhere('contact', 'like', "%$zoneChercher%")
            ->simplePaginate(10);
        // Calculer le nombre total de clients dans ce dépôt
        $nombreFournisseurs = $fournisseurs->count();
        // Retourne la vue avec les résultats de recherche
        return view('fournisseur', compact('fournisseurs','nombreFournisseurs','zoneChercher'));
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
            'intitule' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            // Pas de validation pour 'solde', car vous allez le définir vous-même
        ]);

        // Ajouter l'idDepot et solde (avec la valeur par défaut de 0)
        $validatedData['idDepot'] = $depotId;
     // Définir la valeur par défaut pour le solde

        // Créer un nouveau client avec les données validées et l'idDepot
        Fournisseur::create($validatedData);

        // Rediriger vers la liste des clients avec un message de succès
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur ajouté avec succès.');
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
            'intitule' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            // Pas de validation pour 'solde', car vous allez le définir vous-même
        ]);

        // Ajouter l'idDepot et solde (avec la valeur par défaut de 0)
        $validatedData['idDepot'] = $depotId;
     // Définir la valeur par défaut pour le solde

        // Créer un nouveau client avec les données validées et l'idDepot
        Fournisseur::create($validatedData);

        // Rediriger vers la liste des clients avec un message de succès
        return redirect()->route('factureAchat')->with('success', 'Fournisseur ajouté avec succès.');
    }
    public function destroy($id)
    {
        // Rechercher le client par son ID
        $fournisseur = Fournisseur::find($id);

        if (!$fournisseur) {
            // Si le client n'existe pas, rediriger avec un message d'erreur
            return redirect()->back()->with('error', 'Client non trouvé.');
        }

        // Supprimer le client
        $fournisseur->delete();

        // Rediriger avec un message de succès
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur supprimé avec succès.');
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
        $fournisseur = Fournisseur::find($id);

        if (!$fournisseur) {
            // Si le client n'existe pas, rediriger avec un message d'erreur
            return redirect()->back()->with('error', 'Fournisseur non trouvé.');
        }

        // Mise à jour des informations du client
        $fournisseur->update([
            'intitule' => $validatedData['intituleModif'],
            'contact' => $validatedData['contactModif'],
            'adresse' => $validatedData['adresseModif'],
        ]);

        // Rediriger avec un message de succès
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur modifié avec succès.');
    }
}
