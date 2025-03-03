<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Validation\Rules\Password;

/**
 * Controller pro správu uživatelského profilu
 * Zajišťuje kompletní správu profilu včetně osobních údajů, nastavení a zabezpečení
 */
class ProfileController extends Controller
{
    
    // TODO: Přidat správu profilových obrázků
    // TODO: Implementovat export osobních údajů (GDPR)
    // FIXME: Opravit synchronizaci nastavení mezi zařízeními
    // SECURITY: Přidat dvoufaktorovou autentizaci
    // PERFORMANCE: Optimalizovat ukládání uživatelských preferencí
    // NOTE: Centrální místo pro správu uživatelského účtu
     /**
     * Zobrazí stránku s profilem uživatele
     * 
     * @param Request $request HTTP požadavek obsahující případný tab parametr
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        return Inertia::render('Account/Index', [
            'user' => Auth::user()->load('parameters'),
            'tab' => $request->query('tab', 'profile')
        ]);
    }

    /**
     * Aktualizuje základní profilové informace
     * 
     * @param Request $request HTTP požadavek s novými údaji
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $request->user()->update($validated);

        return back()->with('success', 'Profil byl úspěšně aktualizován.');
    }

    /**
     * Změní heslo uživatele
     * 
     * @param Request $request HTTP požadavek obsahující staré a nové heslo
     * @return \Illuminate\Http\RedirectResponse
     * 
     * Bezpečnostní prvky:
     * - Ověření současného hesla
     * - Validace nového hesla podle bezpečnostních pravidel
     * - Hashování hesla
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Heslo bylo úspěšně změněno.');
    }

    /**
     * Aktualizuje emailovou adresu uživatele
     * 
     * @param Request $request HTTP požadavek s novým emailem
     * @return \Illuminate\Http\RedirectResponse
     * 
     * Metoda:
     * 1. Validuje nový email
     * 2. Aktualizuje email
     * 3. Resetuje verifikační status
     * 4. Odesílá nový verifikační email
     */
    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
        ]);

        $request->user()->update([
            'email' => $validated['email'],
            'email_verified_at' => null
        ]);

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Email byl úspěšně změněn. Na váš nový email byl odeslán ověřovací odkaz.');
    }

    /**
     * Aktualizuje obecná nastavení uživatele
     * 
     * @param Request $request HTTP požadavek s novými nastaveními
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'language' => ['required', 'string', 'in:cs,en'],
            'theme' => ['required', 'string', 'in:light,dark,system'],
        ]);

        $settings = $this->updateUserParameters($request->user(), $validated);

        return back()->with([
            'success' => 'Nastavení bylo úspěšně aktualizováno.',
            'settings' => $settings
        ]);
    }

    /**
     * Aktualizuje nastavení notifikací
     * 
     * @param Request $request HTTP požadavek s novými nastaveními notifikací
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => ['required', 'boolean'],
            'push_notifications' => ['required', 'boolean'],
            'newsletter' => ['required', 'boolean'],
        ]);

        $settings = $this->updateUserParameters($request->user(), $validated);

        return back()->with([
            'success' => 'Nastavení notifikací bylo úspěšně aktualizováno.',
            'settings' => $settings
        ]);
    }

    /**
     * Aktualizuje bezpečnostní nastavení
     * 
     * @param Request $request HTTP požadavek s novými bezpečnostními nastaveními
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'login_notifications' => ['required', 'boolean'],
        ]);

        $settings = $this->updateUserParameters($request->user(), $validated);

        return back()->with([
            'success' => 'Nastavení zabezpečení bylo úspěšně aktualizováno.',
            'settings' => $settings
        ]);
    }

    /**
     * Načte parametry přihlášeného uživatele
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchParameters()
    {
        $user = Auth::user();
        $parameters = $user->parameters()->firstOrCreate([
            'user_id' => $user->id
        ]);

        return response()->json([
            'parameters' => $parameters
        ]);
    }

    /**
     * Pomocná metoda pro aktualizaci uživatelských parametrů
     * 
     * @param \App\Models\User $user
     * @param array $newSettings
     * @return array
     */
    private function updateUserParameters($user, array $newSettings)
    {
        $settings = $user->parameters()->firstOrCreate([
            'user_id' => $user->id
        ]);

        $currentSettings = json_decode($settings->settings ?? '{}', true);
        $mergedSettings = array_merge($currentSettings, $newSettings);

        $settings->update([
            'settings' => json_encode($mergedSettings)
        ]);

        return $mergedSettings;
    }
} 