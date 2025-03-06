<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackNewLogin
{
    public function handle(Request $request, Closure $next)
    {
       
        // Standardní zpracování
        $response = $next($request);
        
        // Kontrola, zda je v session označení o novém přihlášení
        if ($request->session()->has('auth.login_triggered')) {
            Log::info('Nalezen příznak nového přihlášení. Odstraňuji ho a nastavuji flash zprávu.');
            $request->session()->forget('auth.login_triggered');
            $request->session()->flash('success', 'new_login');
        } else {
            Log::info('TrackNewLogin middleware: Žádné označení nového přihlášení nenalezeno');
        }
        
        return $response;
    }
}