<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Pro přihlášeného uživatele zjistíme jazyk z jeho nastavení
        if (Auth::check()) {
            // Získáme uživatelské parametry z databáze
            $user = Auth::user();
            $parameters = $user->parameters;
            
            if ($parameters) {
                // Parsujeme nastavení
                $settings = json_decode($parameters->settings ?? '{}', true);
                
                // Pokud má uživatel nastavení jazyka, použijeme ho
                if (isset($settings['language'])) {
                    $locale = $settings['language'];
                    App::setLocale($locale);
                }
            }
        }
        // 2. Pro nepřihlášeného uživatele zkontrolujeme session
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
            App::setLocale($locale);
        }
        // 3. Pokud není jazyk v session, zjistíme ho z hlavičky prohlížeče
        else {
            $preferredLanguages = $request->getLanguages();
            $availableLocales = config('app.available_locales', ['cs', 'en']);
            
            foreach ($preferredLanguages as $language) {
                // Formát může být např. 'cs-CZ', bereme jen první část
                $locale = substr($language, 0, 2);
                
                if (in_array($locale, $availableLocales)) {
                    App::setLocale($locale);
                    Session::put('locale', $locale);
                    break;
                }
            }
        }

        return $next($request);
    }
}
