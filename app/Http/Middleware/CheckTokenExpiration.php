<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class CheckTokenExpiration
{

    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user) {
            // Tentez une requête de validation du token à un endpoint Google qui nécessite l'authentification.
            // Ceci est juste un exemple. Vous devriez remplacer `https://www.googleapis.com/oauth2/v1/userinfo?alt=json`
            // par un endpoint approprié pour votre application.
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $user->access_token,
            ])->get('https://www.googleapis.com/oauth2/v1/userinfo?alt=json');

            // Si le token est expiré, la requête retournera un status 401 (Unauthorized).
            if ($response->status() == 401) {
                // À ce stade, tentez de rafraîchir le token avec le refresh_token si vous l'avez,
                // ou déconnectez l'utilisateur et demandez une nouvelle authentification.
                $refreshed = $this->refreshToken($user);
                if (!$refreshed) {
                    Auth::logout();
                    return redirect('/dashboard')->with('error', 'Votre session a expiré, veuillez vous reconnecter.');
                }
            }
        }

        return $next($request);
    }

    private function refreshToken($user)
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'refresh_token' => $user->refresh_token,
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'grant_type' => 'refresh_token',
        ]);

        if ($response->successful()) {
            $newTokens = $response->json();
            $user->access_token = $newTokens['access_token'];
            $user->save();

            return true;
        }

        return false;
    }
}
