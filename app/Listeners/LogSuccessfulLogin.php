<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Mail;
use Jenssegers\Agent\Agent;
use App\Models\LoginHistory;
use App\Mail\LoginNotification;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $agent = new Agent();
        $now = now();
        $ipAddress = Request::ip();
        $userAgent = Request::header('User-Agent');
        
        // Získání lokace z IP adresy
        $locationData = $this->getLocationFromIp($ipAddress);
        
        // Vložení záznamu o přihlášení do tabulky login_history pomocí modelu
        $loginRecord = LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'location' => $locationData['location'] ?? null,
            'city' => $locationData['city'] ?? null,
            'country' => $locationData['country'] ?? null,
            'is_suspicious' => $this->detectSuspiciousLogin($user, $ipAddress, $userAgent),
            'notified' => false,
            'status' => 'success',
        ]);
        
        // Kontrola, zda má být odeslána notifikace o přihlášení
        if ($this->shouldSendLoginNotification($user)) {
            $this->sendLoginNotification($user, $loginRecord);
        }
    }
    
    /**
     * Určení typu zařízení.
     */
    private function getDeviceType(Agent $agent): string
    {
        if ($agent->isPhone()) {
            return 'Mobilní telefon';
        } elseif ($agent->isTablet()) {
            return 'Tablet';
        } elseif ($agent->isDesktop()) {
            return 'Počítač';
        } else {
            return 'Neznámé zařízení';
        }
    }
    
    /**
     * Získání přibližné lokace z IP adresy.
     */
    private function getLocationFromIp(string $ip): array
    {
        try {
            if ($ip == '127.0.0.1' || $ip == '::1') {
                return [
                    'location' => 'Lokální prostředí',
                    'city' => null,
                    'country' => null
                ];
            }
            
            $response = file_get_contents("https://freegeoip.app/json/{$ip}");
            $data = json_decode($response, true);
            
            if ($data && isset($data['city']) && isset($data['country_name'])) {
                return [
                    'location' => $data['city'] . ', ' . $data['country_name'],
                    'city' => $data['city'],
                    'country' => $data['country_name']
                ];
            }
            
            return [
                'location' => null,
                'city' => null,
                'country' => null
            ];
        } catch (\Exception $e) {
            return [
                'location' => null,
                'city' => null,
                'country' => null
            ];
        }
    }
    
    /**
     * Detekce podezřelého přihlášení.
     */
    private function detectSuspiciousLogin($user, $ipAddress, $userAgent): bool
    {
        // Zde bychom mohli implementovat logiku pro detekci podezřelého přihlášení
        // Například kontrola, zda se uživatel nepřihlašuje z neobvyklé lokace
        // nebo zda se nepřihlašuje v neobvyklém čase
        
        // Pro jednoduchost zatím vracíme false
        return false;
    }
    
    /**
     * Kontrola, zda má být odeslána notifikace o přihlášení.
     */
    private function shouldSendLoginNotification($user): bool
    {
        // Načtení parametru z user_parameters
        $parameters = DB::table('user_parameters')
            ->where('user_id', $user->id)
            ->first();
        
        if (!$parameters) {
            return false;
        }
        
        $settings = json_decode($parameters->settings, true);
        
        return isset($settings['login_notifications']) && $settings['login_notifications'] === true;
    }
    
    /**
     * Odeslání notifikace o přihlášení.
     */
    private function sendLoginNotification($user, LoginHistory $loginRecord): void
    {
        try {
            // Odesílání e-mailu s notifikací
            Mail::to($user->email)->send(new LoginNotification($user, $loginRecord));
            
            // Aktualizace záznamu o přihlášení - Boolean TRUE
            $loginRecord->notified = true;
            $loginRecord->save();
            
            \Log::info("Notifikace o přihlášení odeslána uživateli {$user->email} (IP: {$loginRecord->ip_address})");
        } catch (\Exception $e) {
            \Log::error("Chyba při odesílání notifikace o přihlášení uživateli {$user->email}: " . $e->getMessage());
            
            // Aktualizace záznamu o přihlášení v případě chyby - Boolean FALSE
            $loginRecord->notified = false;
            $loginRecord->save();
        }
    }
}
