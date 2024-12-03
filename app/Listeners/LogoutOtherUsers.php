<?php

namespace App\Listeners;

use App\Events\UserPasswordChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
class LogoutOtherUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserPasswordChanged  $event
     * @return void
     */
    public function handle(UserPasswordChanged $event)
    {
        $users = \App\Models\User::where('id', '!=', $event->userId)->get();

        foreach ($users as $user) {
            // Déconnecter l'utilisateur en invalidant sa session
            \Illuminate\Support\Facades\Auth::logoutOtherDevices($user->password);
        }
    }
}
