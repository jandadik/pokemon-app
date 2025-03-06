<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use WorkOS\SSO;
use WorkOS\WorkOS;

class WorkOSController extends Controller
{
    protected $sso;

    public function __construct()
    {
        // Inicializace WorkOS SSO
        $apiKey = config('services.workos.api_key');
        $clientId = config('services.workos.client_id');
        
        // Nastavení API klíče a Client ID pro WorkOS
        WorkOS::setApiKey($apiKey);
        WorkOS::setClientId($clientId);
        
        // Inicializace SSO modulu
        $this->sso = new SSO();
    }

    /**
     * Zahájí autentifikaci pomocí WorkOS
     */
    public function redirect(Request $request)
    {
        // Pro OAuth (Google) používáme poskytovatele místo connection_id
        $provider = 'GoogleOAuth';
        
        // Získání správné URL pro callback
        $redirectUri = url('/authenticate');
        
        // Výpis aktuální hodnoty redirect_uri pro ladění
        \Log::info('WorkOS použije redirect URI: ' . $redirectUri);
        
        // Vytvoření state pole místo řetězce (SDK provádí json_encode na state parametru)
        $state = ['session_id' => session()->getId()];
        
        // Vytvoření autorizační URL - pro OAuth používáme parametr provider
        $authorizationUrl = $this->sso->getAuthorizationUrl(
            null, // domain (není potřeba)
            $redirectUri, // Použití dynamicky vygenerované URL pro redirect
            $state, // state jako pole, které SDK zkonvertuje na validní JSON
            $provider, // provider - pro Google OAuth použijeme 'GoogleOAuth'
            null // connection (není potřeba pro OAuth)
        );

        // Pro debugging: logovací informace
        \Log::info('Vygenerovaná WorkOS URL: ' . $authorizationUrl);
        
        // Jednoduché přesměrování - obchází CORS
        return redirect()->away($authorizationUrl);
    }

    /**
     * Zpracování callback po úspěšné autentifikaci
     */
    public function callback(Request $request)
    {
        \Log::info('WorkOS callback - Všechny parametry: ' . json_encode($request->all()));
        
        // Kontrola, zda přišla chyba z WorkOS
        if ($request->has('error')) {
            $error = $request->query('error');
            $errorDescription = $request->query('error_description');
            \Log::error('WorkOS vrátil chybu: ' . $error . ' - ' . $errorDescription);
            return redirect()->route('login')->with('error', 'Přihlášení přes WorkOS selhalo: ' . $errorDescription);
        }
        
        // Získání kódu z WorkOS
        $code = $request->query('code');
        \Log::info('WorkOS callback - Parametr code: ' . ($code ?: 'není nastaven'));
        
        if (!$code) {
            \Log::error('WorkOS error: Parametr code chybí v callback požadavku');
            return redirect()->route('login')->with('error', 'Přihlášení přes WorkOS selhalo: Chybí autorizační kód');
        }
        
        // Získání a dekódování state parametru, který je JSON objekt
        $state = $request->query('state');
        \Log::info('Přijatý state: ' . $state);
        
        try {
            // Výměna kódu za profil
            $profileAndToken = $this->sso->getProfileAndToken($code);
            $profile = $profileAndToken->profile;
            
            \Log::info('Profil z WorkOS: ' . json_encode($profile));

            // Najdeme uživatele podle emailu nebo vytvoříme nového
            $user = User::firstOrNew(['email' => $profile->email]);
            
            if (!$user->exists) {
                // Vytvoření nového uživatele
                $user->fill([
                    'name' => $profile->firstName . ' ' . $profile->lastName,
                    'email' => $profile->email,
                    'password' => Hash::make(Str::random(16)), // Náhodné heslo
                ]);
                $user->save();
                
                \Log::info('Vytvořen nový uživatel: ' . $profile->email);
            } else {
                \Log::info('Nalezen existující uživatel: ' . $profile->email);
            }

            // Přihlášení uživatele
            Auth::login($user);
            \Log::info('Uživatel přihlášen: ' . $profile->email);

            // Přesměrování na dashboard
            return redirect()->intended('/');
            
        } catch (\Exception $e) {
            // Něco se pokazilo
            \Log::error('WorkOS error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('login')->with('error', 'Přihlášení přes WorkOS selhalo: ' . $e->getMessage());
        }
    }
} 