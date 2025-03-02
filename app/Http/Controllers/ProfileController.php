<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Account/Index', [
            'user' => Auth::user()->load('parameters'),
            'tab' => $request->query('tab', 'profile')
        ]);
    }

    public function updateProfile(Request $request)
    {
        // \Log::info('Přijatá data:', $request->all());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        // \Log::info('Validovaná data:', $validated);

        $request->user()->update($validated);

        // \Log::info('Uživatel po aktualizaci:', $request->user()->toArray());

        return back()->with('success', 'Profil byl úspěšně aktualizován.');
    }

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

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'language' => ['required', 'string', 'in:cs,en'],
            'theme' => ['required', 'string', 'in:light,dark,system'],
        ]);

        $settings = $request->user()->parameters()->firstOrCreate([
            'user_id' => $request->user()->id
        ]);

        $currentSettings = json_decode($settings->settings ?? '{}', true);
        $newSettings = array_merge($currentSettings, $validated);

        $settings->update([
            'settings' => json_encode($newSettings)
        ]);

        return back()->with([
            'success' => 'Nastavení bylo úspěšně aktualizováno.',
            'settings' => $newSettings
        ]);
    }

    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => ['required', 'boolean'],
            'push_notifications' => ['required', 'boolean'],
            'newsletter' => ['required', 'boolean'],
        ]);

        $settings = $request->user()->parameters()->firstOrCreate([
            'user_id' => $request->user()->id
        ]);

        $currentSettings = json_decode($settings->settings ?? '{}', true);
        $newSettings = array_merge($currentSettings, $validated);

        $settings->update([
            'settings' => json_encode($newSettings)
        ]);

        return back()->with([
            'success' => 'Nastavení notifikací bylo úspěšně aktualizováno.',
            'settings' => $newSettings
        ]);
    }

    public function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'login_notifications' => ['required', 'boolean'],
            //'two_factor_enabled' => ['required', 'boolean'],
        ]);

        $settings = $request->user()->parameters()->firstOrCreate([
            'user_id' => $request->user()->id
        ]);

        $currentSettings = json_decode($settings->settings ?? '{}', true);
        $newSettings = array_merge($currentSettings, $validated);

        $settings->update([
            'settings' => json_encode($newSettings)
        ]);

        return back()->with([
            'success' => 'Nastavení zabezpečení bylo úspěšně aktualizováno.',
            'settings' => $newSettings
        ]);
    }

    /**
     * Načte parametry přihlášeného uživatele
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
} 