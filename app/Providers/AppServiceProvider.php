<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\Transfert;
use App\Models\Depot;
use App\Models\FactureVente;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Validator::extend('check_intervalles', function ($attribute, $value, $parameters, $validator) {
            $quantiteMin1 = $validator->getData()['quantiteMinModif1'];
            $quantiteMax1 = $validator->getData()['quantiteMaxModif1'];
            $quantiteMin2 = $validator->getData()['quantiteMinModif2'];
            $quantiteMax2 = $validator->getData()['quantiteMaxModif2'];
            $quantiteMin3 = $validator->getData()['quantiteMinModif3'];
            $quantiteMax3 = $validator->getData()['quantiteMaxModif3'];
    
            // Vérifier l'intersection des intervalles
            if (($quantiteMin1 != 0 || $quantiteMax1 != 0) &&
                ($quantiteMin2 != 0 || $quantiteMax2 != 0) &&
                ($quantiteMin3 != 0 || $quantiteMax3 != 0)) {
                // Vérifier l'intersection des intervalles
                if (($quantiteMin1 <= $quantiteMax2 && $quantiteMax1 >= $quantiteMin2) ||
                    ($quantiteMin1 <= $quantiteMax3 && $quantiteMax1 >= $quantiteMin3)) {
                    return false; // Il y a une intersection avec l'intervalle 1
                }
                
                if (($quantiteMin2 <= $quantiteMax1 && $quantiteMax2 >= $quantiteMin1) ||
                    ($quantiteMin2 <= $quantiteMax3 && $quantiteMax2 >= $quantiteMin3)) {
                    return false; // Il y a une intersection avec l'intervalle 2
                }
                
                if (($quantiteMin3 <= $quantiteMax1 && $quantiteMax3 >= $quantiteMin1) ||
                    ($quantiteMin3 <= $quantiteMax2 && $quantiteMax3 >= $quantiteMin2)) {
                    return false; // Il y a une intersection avec l'intervalle 3
                }
            }
    
            return true; // Pas d'intersection
        });
        View::composer('*', function ($view) {
            // Supposons que vous ayez une session utilisateur qui a accès à un dépôt spécifique
            $depotIntitule = session('depotValue');
        
            // Comptage des transferts en attente
            if ($depotIntitule) {
                $depot = Depot::where('intitule', $depotIntitule)->first();
        
                $transfertsAttenteCount = Transfert::where('statut', 'en attente')
                    ->whereHas('depotDestination', function($query) use ($depotIntitule) {
                        $query->where('intitule', $depotIntitule);
                    })
                    ->count();
        
                // Comptage des factures de vente en attente pour le dépôt sélectionné
                if ($depot) {
                    $facturesVenteAttenteCount = FactureVente::where('statut', 'en attente')
                        ->where('idDepot', $depot->id) // Filtrer par le dépôt en session
                        ->count();
        
                    // Comptage des factures de vente en échéance pour le dépôt sélectionné
                    $facturesVenteEcheanceCount = FactureVente::where('statut', 'non payee')
                        ->where('idDepot', $depot->id)
                        ->where('dateEcheance', '<', now()) // Factures dont la date d'échéance est passée
                        ->count();
                } else {
                    $facturesVenteAttenteCount = 0;
                    $facturesVenteEcheanceCount = 0;
                }
        
            } else {
                $transfertsAttenteCount = 0; // Par défaut à 0 si pas de dépôt sélectionné
                $facturesVenteAttenteCount = 0; // Par défaut à 0 si pas de dépôt sélectionné
                $facturesVenteEcheanceCount = 0; // Par défaut à 0 si pas de dépôt sélectionné
            }
        
            // Partager les compteurs avec toutes les vues
            $view->with([
                'transfertsAttenteCount' => $transfertsAttenteCount,
                'facturesVenteAttenteCount' => $facturesVenteAttenteCount,
                'facturesVenteEcheanceCount' => $facturesVenteEcheanceCount,
            ]);
        });
        
    }
}
