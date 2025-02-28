<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthenticationController extends Controller
{
    public function generateQrCode()
    {
        $google2fa = new Google2FA();
        
        $user = Auth::user();
        $secretKey = $google2fa->generateSecretKey();
        
        // Uložíme secret key do session pro pozdější použití při aktivaci
        session(['2fa_secret' => $secretKey]);
        
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secretKey
        );
        
        return response()->json(['qr_code' => $qrCodeUrl]);
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:6'
        ]);

        $google2fa = new Google2FA();
        $secretKey = session('2fa_secret');
        
        // Ověříme zadaný kód
        $valid = $google2fa->verifyKey($secretKey, $request->code);
        
        if ($valid) {
            $user = Auth::user();
            $user->two_factor_secret = encrypt($secretKey);
            $user->two_factor_enabled = true;
            $user->save();
            
            session()->forget('2fa_secret');
            
            return back()->with('success', 'Dvoufaktorové ověření bylo úspěšně aktivováno.');
        }
        
        return back()->withErrors(['code' => 'Neplatný ověřovací kód.']);
    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $user->two_factor_secret = null;
        $user->two_factor_enabled = false;
        $user->save();
        
        return back()->with('success', 'Dvoufaktorové ověření bylo deaktivováno.');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:6'
        ]);

        $google2fa = new Google2FA();
        $user = Auth::user();
        $valid = $google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $request->code
        );
        
        if ($valid) {
            session(['2fa_verified' => true]);
            return redirect()->intended();
        }
        
        return back()->withErrors(['code' => 'Neplatný ověřovací kód.']);
    }
} 