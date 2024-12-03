<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckInactivity
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user) {
            $lastActivity = $user->last_activity; // Supposons que vous avez un champ 'last_activity' dans votre modèle User
            $inactiveTime = 3600; // Durée d'inactivité en secondes (180 secondes = 3 minutes)
            //$inactiveTime = 60;

            if (Carbon::now()->diffInSeconds($lastActivity) > $inactiveTime) {
                $user->last_activity = Carbon::now();
                $user->save();
                Auth::logout(); // Déconnexion de l'utilisateur
                return redirect()->route('auth.login')->with('inactive', 'Votre session a expiré en raison d\'inactivité.');
            } else {
                // Mettre à jour le champ last_activity pour l'utilisateur
                $user->last_activity = Carbon::now();
                $user->save();
            }
        }

        return $next($request);
    }
}
