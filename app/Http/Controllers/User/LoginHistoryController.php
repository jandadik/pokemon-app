<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Log;

/**
 * Controller pro správu historie přihlášení
 * Sleduje a zobrazuje historii přihlášení uživatele včetně detailů o zařízeních a lokacích
 */
class LoginHistoryController extends Controller
{
    // TODO: Vytvořit Resource třídu pro transformaci dat (LoginHistoryResource)
    // TODO: Implementovat export historie do PDF/CSV
    // SECURITY: Přidat šifrování citlivých údajů o zařízeních
    // PERFORMANCE: Optimalizovat ukládání a načítání historie
    // NOTE: Historie přihlášení je důležitá pro bezpečnostní audit
    // TODO: Implementovat stránkování historie přihlášení
    // TODO: Přidat filtrování podle data a zařízení
    // SECURITY: Přidat detekci podezřelých přihlášení
    // PERFORMANCE: Optimalizovat agregaci dat pro grafy

    /**
     * Zobrazí historii přihlášení pro aktuálního uživatele
     * 
     * @param Request $request HTTP požadavek
     * @return \Inertia\Response|\Illuminate\Http\JsonResponse
     * 
     * Metoda:
     * 1. Načte posledních 10 záznamů přihlášení
     * 2. Transformuje data pro zobrazení
     * 3. Označí aktuální session
     * 4. Vrací data podle požadovaného formátu (JSON/Inertia)
     * 
     * Bezpečnostní prvky:
     * - Filtrování podle user_id (každý vidí jen své přihlášení)
     * - Limitovaný počet záznamů (ochrana proti přetížení)
     * - Logování přístupů pro audit
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        Log::info("Načítání historie přihlášení pro uživatele: {$userId}");
        
        $history = LoginHistory::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        Log::info("Nalezeno {$history->count()} záznamů historie přihlášení");
            
        $loginHistory = $history->map(function ($item) {
            $currentSession = (request()->ip() === $item->ip_address && 
                              request()->header('User-Agent') === $item->user_agent);
            
            return [
                'id' => $item->id,
                'ip_address' => $item->ip_address,
                'user_agent' => $item->user_agent,
                'location' => $item->location ?: 'Neznámá lokace',
                'city' => $item->city,
                'country' => $item->country,
                'is_suspicious' => (bool) $item->is_suspicious,
                'notified' => (bool) $item->notified,
                'status' => $item->status,
                'created_at' => $item->created_at,
                'is_current' => $currentSession,
            ];
        });
        
        if ($request->wantsJson()) {
            Log::info("Vracím JSON odpověď s {$loginHistory->count()} záznamy");
            return response()->json(['history' => $loginHistory]);
        }
        
        Log::info("Vracím Inertia odpověď s {$loginHistory->count()} záznamy");
        return Inertia::render('Account/Index', [
            'loginHistory' => $loginHistory
        ]);
    }
}
