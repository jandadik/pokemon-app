<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Log;

class LoginHistoryController extends Controller
{
    /**
     * Získání historie přihlášení pro aktuálního uživatele.
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
