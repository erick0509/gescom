<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Depot;
use App\Events\UserPasswordChanged;
use Carbon\Carbon;
class AuthController extends Controller
{
    
    public function login()
    {
        return view('auth.login');
    }
    public function logout()
    {
        //dd(Auth::user());
        Auth::logout();
        return redirect()->route('auth.login');
    }
    public function doLogin(Request $request)
    {
        $request->validate(
            [
                "name"=>"required",
                "password"=>"required|min:4"
            ]
           );
           $credentials = $request->only('name', 'password');
           // Tentative d'authentification
           
           if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $user->last_activity = Carbon::now();
                $user->save();
                $request->session()->put('name', $request->input('name'));
                return redirect()->intended(route('accueil'));
           } else {
               // Authentification échouée
               return back()->withErrors(['user' => 'Identifiant ou mots de passe incorrect'])->onlyInput('name');
           }
    }
    public function update(Request $request)
    {
        // Valider les données reçues du formulaire
        $request->validate([
            'oldPassword' => 'required|min:4', // Ancien mot de passe requis et longueur minimale de 4 caractères
            'newPassword' => 'required|min:4', // Nouveau mot de passe requis et longueur minimale de 4 caractères
            'renewPassword' => 'required|same:newPassword', // Confirmation du nouveau mot de passe requise et doit correspondre au nouveau mot de passe
        ]);

        // Vérifier si l'ancien mot de passe correspond au mot de passe actuel de l'utilisateur
        if (!Hash::check($request->oldPassword, Auth::user()->password)) {
            return redirect()->back()->withErrors(['oldPassword' => 'Le mot de passe actuel est incorrect.'])->withInput();
        }

        // Mettre à jour le mot de passe de l'utilisateur
        $user = Auth::user();
        $user->password = Hash::make($request->newPassword);
        $user->save();
        //event(new UserPasswordChanged(Auth::id()));
        // Rediriger avec un message de succès
        return redirect()->route('accueil')->with('success', 'Votre mot de passe a été modifié avec succès.');
    }
    public function reinitialiser()
    {
        $adminUser = User::where('name', 'admin')->first();
        $depots = Depot::all();

        if ($adminUser) {
            // Réinitialiser le mot de passe de l'administrateur
            $adminUser->password = Hash::make('0000');
            $adminUser->save();
            foreach ($depots as $depot) {
                $depot->code_acces = Hash::make('0000');
                $depot->save();
            }
            return redirect()->route('auth.login')->with('success', 'Le mot de passe de l\'administrateur a été réinitialisé avec succès.');
        }
        
    }
    public function parametre()
    {
        return view('auth.setting');
    }
    public function checkCodeAcces(Request $request)
    {
        // Récupérer l'utilisateur actuellement authentifié
        $adminUser = User::where('name', 'admin')->first();  
        if ($adminUser && Hash::check($request->code_acces, $adminUser->code_acces)) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }
    public function checkCodeAccesDepot(Request $request)
    {
        $codeAcces = $request->input('code_acces');
        $depotIntitule = $request->input('depot_intitule'); // Récupérer l'intitulé du dépôt depuis la requête

        // Récupérer le code d'accès stocké dans la base de données pour le dépôt
        
        $depot = Depot::where('intitule', $depotIntitule)->first();
        
        if ($depot && Hash::check($codeAcces, $depot->code_acces)) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false,'data'=>$depotIntitule,'code'=>$codeAcces]);
    }
}
