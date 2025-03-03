<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

/**
 * Controller pro správu autentizace uživatelů
 * Zajišťuje přihlášení, odhlášení a zobrazení přihlašovacího formuláře
 */
class AuthController extends Controller
{
    /**
     * Zobrazí přihlašovací formulář
     * 
     * @return \Inertia\Response Renderuje Vue komponentu Auth/Login
     */
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Zpracuje přihlášení uživatele
     * 
     * @param Request $request HTTP požadavek obsahující přihlašovací údaje
     * @return \Illuminate\Http\RedirectResponse
     * @throws ValidationException Pokud jsou přihlašovací údaje nesprávné
     * 
     * Metoda:
     * 1. Validuje email a heslo
     * 2. Pokusí se přihlásit uživatele (remember me je vždy true)
     * 3. Při úspěchu přesměruje na zamýšlenou stránku nebo na index
     * 4. Při neúspěchu vyhodí ValidationException s chybovou zprávou
     */
    public function store(Request $request)
    {
        if (!Auth::attempt($request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]), true)) {
            throw ValidationException::withMessages([
                'email' => 'Nesprávné přihlašovací údaje.',
                'password' => 'Nesprávné přihlašovací údaje.',
            ]);
        }
        
        $request->session()->regenerate();

        return redirect()->intended(route('index'));
    }

    /**
     * Odhlásí přihlášeného uživatele
     * 
     * @param Request $request HTTP požadavek
     * @return \Illuminate\Http\RedirectResponse
     * 
     * Metoda:
     * 1. Odhlásí uživatele
     * 2. Zneplatní session
     * 3. Vygeneruje nový CSRF token
     * 4. Přesměruje na hlavní stránku
     */
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('index');
    }
}
