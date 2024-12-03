<?php
namespace App\Listeners;

use App\Events\UserPasswordChanged;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserPasswordChangedListener implements ShouldQueue
{
    public function handle(UserPasswordChanged $event)
    {
        // Récupérer tous les utilisateurs connectés excepté celui qui a changé son mot de passe
        $users = \App\Models\User::where('id', '!=', $event->userId)->get();

        foreach ($users as $user) {
            // Déconnecter l'utilisateur en invalidant sa session
            \Illuminate\Support\Facades\Auth::logoutOtherDevices($user->password);
        }
    }
}
