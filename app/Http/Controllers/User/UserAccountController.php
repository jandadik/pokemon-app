<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

/**
 * Controller pro správu uživatelských účtů
 * Zajišťuje vytváření nových uživatelských účtů a jejich základní nastavení
 */
class UserAccountController extends Controller
{
    
    // TODO: Implementovat sociální přihlášení (Google, Facebook)
    // TODO: Přidat možnost pozvání nových uživatelů
    // FIXME: Opravit validaci hesla při registraci
    // SECURITY: Implementovat rate limiting pro registrace
    // PERFORMANCE: Optimalizovat proces vytváření účtu
    // NOTE: Vstupní bod pro nové uživatele systému
    /** 
     * Zobrazí formulář pro vytvoření nového uživatelského účtu
     * 
     * @return \Inertia\Response Renderuje Vue komponentu UserAccount/Create
     */
    public function create()
    {
        return Inertia::render('UserAccount/Create');
    }

    /**
     * Zpracuje vytvoření nového uživatelského účtu
     * 
     * @param Request $request HTTP požadavek obsahující registrační údaje
     * @return \Illuminate\Http\RedirectResponse
     * 
     * Metoda:
     * 1. Validuje vstupní data (jméno, email, heslo)
     * 2. Vytvoří nového uživatele
     * 3. Přiřadí základní roli 'user'
     * 4. Automaticky přihlásí uživatele
     * 5. Přesměruje na hlavní stránku
     * 
     * Bezpečnostní prvky:
     * - Validace vstupních dat
     * - Hashování hesla
     * - Regenerace session po přihlášení
     * - Přiřazení základní role pro omezení práv
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $user->assignRole('user');
        $request->session()->regenerate();

        return redirect()->intended(route('index'))
            ->with('success', 'Účet byl úspěšně vytvořen.');
    }
}
