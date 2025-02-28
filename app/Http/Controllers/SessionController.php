<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    public function destroy($sessionId)
    {
        if ($sessionId === session()->getId()) {
            return response()->json([
                'error' => 'Nelze ukončit aktuální session.'
            ], 400);
        }

        DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json([
            'message' => 'Session byla úspěšně ukončena.'
        ]);
    }
} 