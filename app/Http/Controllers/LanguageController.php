<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Přepne jazyk aplikace
     *
     * @param Request $request
     * @param string $locale Jazyk (např. 'cs', 'en')
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request, string $locale)
    {
        // Ověříme, že jazyk je jeden z podporovaných
        $availableLocales = config('app.available_locales', ['cs', 'en']);
        
        if (!in_array($locale, $availableLocales)) {
            return redirect()->back()->with('error', 'Nepodporovaný jazyk.');
        }
        
        // Pro přihlášeného uživatele aktualizujeme nastavení v profilu
        if (Auth::check()) {
            $user = Auth::user();
            $parameters = $user->parameters;
            
            if ($parameters) {
                $settings = json_decode($parameters->settings ?? '{}', true);
                $settings['language'] = $locale;
                
                $parameters->settings = json_encode($settings);
                $parameters->save();
            }
        }
        
        // Ukládáme jazyk do session (pro přihlášené i nepřihlášené uživatele)
        Session::put('locale', $locale);
        
        // Přesměrujeme zpět na předchozí stránku
        return redirect()->back();
    }
}
