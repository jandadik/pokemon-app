<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

/**
 * Controller pro správu uživatelských sessions
 * Umožňuje uživatelům spravovat jejich aktivní sessions (přihlášení)
 */
class SessionController extends Controller
{
    // TODO: Přidat zobrazení aktivních sessions pro uživatele
    // TODO: Implementovat hromadné ukončení všech sessions kromě aktuální
    // FIXME: Řešit problém s nekonzistentním stavem sessions
    // SECURITY: Přidat notifikaci při ukončení session na jiném zařízení
    // PERFORMANCE: Optimalizovat dotazy na sessions table
    // NOTE: Kritické pro správu zabezpečení účtu

    /**
     * Ukončí specifikovanou session uživatele
     * 
     * @param string $sessionId ID session, která má být ukončena
     * @return JsonResponse
     * 
     * Metoda:
     * 1. Kontroluje, zda se nejedná o aktuální session
     * 2. Ověřuje existenci session a její vlastnictví
     * 3. Maže session z databáze pouze pokud patří přihlášenému uživateli
     * 4. Vrací JSON odpověď o výsledku operace
     * 
     * Návratové kódy:
     * - 200: Session úspěšně ukončena
     * - 400: Pokus o ukončení vlastní aktivní session
     * - 404: Session neexistuje nebo nepatří uživateli
     * 
     * Bezpečnostní prvky:
     * - Nelze smazat vlastní aktivní session
     * - Kontrola existence a vlastnictví session
     * - Použití parametrizovaného dotazu (ochrana proti SQL injection)
     * - Typová kontrola parametrů a návratových hodnot
     */
    public function destroy(string $sessionId): JsonResponse
    {
        // Kontrola, zda nejde o aktuální session
        if ($sessionId === session()->getId()) {
            return response()->json([
                'error' => 'Nelze ukončit aktuální session.'
            ], 400);
        }

        // Ověření existence session a jejího vlastnictví
        $sessionExists = DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$sessionExists) {
            return response()->json([
                'error' => 'Session nebyla nalezena nebo k ní nemáte přístup.'
            ], 404);
        }

        // Smazání session
        DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json([
            'message' => 'Session byla úspěšně ukončena.'
        ], 200);
    }
} 