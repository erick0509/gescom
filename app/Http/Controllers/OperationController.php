<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Date;
use App\Models\Operation;
use App\Models\Depot;
use App\Models\Caisse;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    public function formOperation()
    {
        
        // Récupérer le dépôt en session
        $depotIntitule = session('depotValue');
        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }
        // Récupérer l'ID du dépôt
        $depotId = $depot->id;
        // Filtrer les opérations par dépôt en session
        $operations = Operation::where('idDepot', $depotId)->with('depot')->simplePaginate(10);
        $caisse = Caisse::where('idDepot', $depotId)->first();
        // Passer les opérations filtrées à la vue
        return view('debitCaisse', compact('operations','caisse'));
    }
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'type' => 'required|in:debiter,crediter',
            'montant' => 'required|numeric|min:0',
            'commentaire' => 'nullable|string|max:255',
        ]);

        // Récupérer le dépôt en session
        $depotIntitule = session('depotValue');
        
        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }

        // Récupérer la caisse associée au dépôt
        $caisse = Caisse::where('idDepot', $depot->id)->first();
        if (!$caisse) {
            return redirect()->back()->with('error', 'Caisse non trouvée pour ce dépôt.');
        }

        // Définir le montant (positif pour débiter, négatif pour créditer)
        $montant = $validated['type'] === 'crediter' ? -$validated['montant'] : $validated['montant'];
        // Vérifier si le montant en caisse est suffisant avant l'opération
        if ($caisse->montant + $montant < 0) {
            // Si le montant en caisse devient négatif, retourner une erreur
            return redirect()->back()->with('error', 'Montant en caisse insuffisant pour cette opération.');
        }
        // Mettre à jour le montant de la caisse
        $caisse->montant += $montant;
        $caisse->save();

        // Créer une nouvelle opération avec la date courante
        Operation::create([
            'idDepot' => $depot->id,
            'type' => $validated['type'],
            'date_operation' => now(), // Utiliser la date actuelle
            'montant' => $montant,
            'commentaire' => $validated['commentaire'],
        ]);

        // Redirection avec un message de succès
        return redirect()->route('debitCaisse')->with('success', 'L\'opération a été enregistrée avec succès et la caisse a été mise à jour.');
    }

    public function search(Request $request)
    {
        // Validation
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'start_date.required' => 'La date de début est obligatoire.',
            'end_date.required' => 'La date de fin est obligatoire.',
            'end_date.after_or_equal' => 'La date de fin doit être égale ou postérieure à la date de début.',
        ]);
        $depotIntitule = session('depotValue');
        // Requête pour récupérer l'ID du dépôt en fonction de son intitulé
        $depot = Depot::where('intitule', $depotIntitule)->first();
        if (!$depot) {
            return redirect()->back()->with('error', 'Dépôt non trouvé.');
        }
        // Récupérer l'ID du dépôt
        $depotId = $depot->id;
        // Logique de récupération des opérations
        $operations = Operation::whereBetween('date_operation', [$request->start_date, $request->end_date])
                                ->where('idDepot', $depotId)
                                ->simplePaginate(10);
        $caisse = Caisse::where('idDepot', $depotId)->first();
        return view('debitCaisse', compact('operations','caisse'));
    }


}
