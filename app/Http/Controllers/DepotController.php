<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use App\Models\Caisse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DepotController extends Controller
{
    public function parametre()
    {
        $depotIntitule = session('depotValue');
        $depot = Depot::where('intitule', $depotIntitule)->first();
        return view('setting-code-acces', compact("depot"));
    }
    public function updateCode(Request $request)
    {
        // Valider les données reçues du formulaire
        $depotIntitule = session('depotValue');
        $request->validate([
            'code_acces' => 'required|min:4', // Ancien mot de passe requis et longueur minimale de 4 caractères
        ]);
        $depot = Depot::where('intitule', $depotIntitule)->first();

        $depot->code_acces = Hash::make($request->code_acces);
        $depot->save();
        //event(new UserPasswordChanged(Auth::id()));
        // Rediriger avec un message de succès
        return redirect()->route('accueil')->with('success', 'Le Code d\'acces du Depot a été modifié avec succès.');
    }
    public function index()
    {
        //$adminUser = User::where('name', 'admin')->first();
        //$adminUser->code_acces = Hash::make('7834');
        //$adminUser->save();
        //$depot1 = Depot::where('intitule', 'DP KINTANA')->first();
        //$depot1->code_acces = Hash::make('0000');
        //$depot1->save();

        //$depot2 = Depot::where('intitule', 'TB KINTANA')->first();
        //$depot2->code_acces = Hash::make('0000');
        //$depot2->save();
        $depots = Depot::orderBy("created_at", "desc")->simplePaginate(6);

        return view('accueil', compact("depots"));
    }
    public function creer(Request $request)
    {
        $request->validate(
            [
                "intitule" => "required|unique:depots|max:255",
                "prefixe" => "required|unique:depots|max:10",
                'type_depot' => 'required|in:principal,secondaire',
            ]
        );
        $isPrincipal = $request->input('type_depot') === 'principal' ? 1 : 0;
        try {
            $depot = Depot::create([
                'intitule' => $request->intitule,
                'type' => null,
                'adresse' => null,
                'prefixe' => $request->prefixe,
                'code_acces' => Hash::make('0000'),
                'principal' => $isPrincipal,
            ]);
            Caisse::create([
                'idDepot' => $depot->id,  // Utilisation de l'id du dépôt nouvellement créé
                'montant' => 0,           // Montant initial 0
            ]);
            return back()->with('successDepot', 'Dépôt ajouté avec succès!');
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) { // Le code d'erreur pour la contrainte de clé unique
                return back()->with('error', 'Le dépôt avec cet intitulé existe déjà!');
            }
            return back()->with('error', 'Une erreur est survenue lors de l\'ajout du dépôt.');
        }
    }
    public function delete(Depot $depot)
    {
        // Vérifier si le dépôt est rattaché à un transfert en tant que source ou destination
        $hasTransfers = $depot->transfertsSource()->exists() || $depot->transfertsDestination()->exists();

        if ($hasTransfers) {
            // Retourner une erreur si le dépôt est lié à un transfert
            return redirect()->route('accueil', ['page' => 1])
                ->with('errorDelete', 'Impossible de supprimer le dépôt car il est rattaché à un transfert.');
        }

        // Supprimer les articles associés au dépôt
        $depot->articles()->detach();

        // Supprimer la caisse associée au dépôt
        $depot->caisse()->delete();

        // Supprimer le dépôt lui-même
        $depot->delete();

        return redirect()->route('accueil', ['page' => 1])
            ->with('successDelete', 'Dépôt supprimé avec succès.');
    }

    public function update(Request $request, Depot $depot)
    {
        $request->validate(
            [
                "intituleModif" => "required",
                "prefixeModif" => "required"
            ]
        );
        $depot->update(
            [
                "intitule" => $request->intituleModif,
                "type" => $request->typeModif,
                "adresse" => $request->adresseModif
            ]
        );
        return back()->with("successUpdate", "Votre Depot/Magasin est a jour!");
    }
    public function rechercherDepot(Request $request)
    {
        $intitule = $request->input('intituleChercher');

        $depots = Depot::where('intitule', 'like', '%' . $intitule . '%')->get();
        $depots = Depot::where('intitule', 'like', '%' . $intitule . '%')
            ->orderBy("intitule", "asc")
            ->simplePaginate(3);
        return view('accueil', compact('depots', 'intitule'));
    }
}
