<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Pokud uživatel není přihlášen nebo nemá aktivované 2FA, pokračujeme
        if (!$user || !$user->two_factor_enabled) {
            return $next($request);
        }

        // Pokud má uživatel aktivované 2FA, ale už ověřil kód v této session, pokračujeme
        if (session('2fa_verified')) {
            return $next($request);
        }

        // Uložíme si původní URL pro pozdější přesměrování
        if (!$request->is('two-factor/*')) {
            session(['url.intended' => $request->url()]);
        }

        // Jinak přesměrujeme na stránku pro ověření 2FA
        return redirect()->route('auth.two-factor.challenge');
    }
}
