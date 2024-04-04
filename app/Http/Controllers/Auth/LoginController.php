<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TemporaryUser;
use App\Models\User;
use Exception;
use Google_Client;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{

    public function rrredirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['https://www.googleapis.com/auth/calendar'])
            ->with(["access_type" => "offline"])
            ->redirect();
    }

    public function redirectToGoogle(){
        $scopes = array(
            'https://www.googleapis.com/auth/calendar.events'
        );

        $parameters = ['access_type' => 'offline', "prompt" => "consent select_account"];

        return Socialite::driver('google')->scopes($scopes)->with($parameters)->redirect();
    }


    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Recherche d'un utilisateur existant par son adresse e-mail
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Création d'un nouvel utilisateur si non trouvé
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                ]);

                $temporaryUser = TemporaryUser::where('email', $user->email)->first();

                if ($temporaryUser) {
                    // Ici, vous pouvez transférer les données de 'temporary_users' vers 'users' ou effectuer d'autres actions nécessaires
                    // Par exemple, attribuer des rendez-vous temporairement réservés à cet utilisateur

                    // Supprimer l'utilisateur temporaire après avoir transféré les données nécessaires
                    $temporaryUser->delete();
                }
            }

            if ($googleUser->refreshToken) {
                $user->google_refresh_token = $googleUser->refreshToken;
                $user->save();
            }

            // Connexion de l'utilisateur
            Auth::login($user, true);

            return redirect()->to('/'); // Redirection vers une route souhaitée après la connexion

        } catch (Exception $e) {
            // Gestion des erreurs
            return redirect('/')->with('error', 'Une erreur est survenue lors de la connexion avec Google.');
        }
    }
}
