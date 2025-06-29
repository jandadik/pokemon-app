<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Překlady pro uživatelský účet
    |--------------------------------------------------------------------------
    |
    | Následující jazykové řádky se používají v sekci uživatelského účtu
    | pro různé položky, štítky a hlášky.
    |
    */

    // Hlavní záložky
    'tabs' => [
        'profile' => 'Osobní údaje',
        'password' => 'Změna hesla',
        'email' => 'Email',
        'notifications' => 'Notifikace',
        'security' => 'Zabezpečení',
        'settings' => 'Nastavení',
        'delete' => 'Smazat účet',
    ],

    // Obecná oznámení
    'general' => [
        'close' => 'Zavřít',
        'cancel' => 'Zrušit',
    ],
    
    'select_section' => 'Vyberte sekci',
    'unverified' => 'Neověřeno',

    // Profil
    'profile' => [
        'title' => 'Osobní údaje',
        'name' => 'Jméno',
        'phone' => 'Telefon',
        'bio' => 'O mně',
        'save' => 'Uložit změny',
        'success_message' => 'Profil byl úspěšně aktualizován',
    ],

    // Heslo
    'password' => [
        'title' => 'Změna hesla',
        'current' => 'Současné heslo',
        'new' => 'Nové heslo',
        'confirm' => 'Potvrzení nového hesla',
        'change' => 'Změnit heslo',
        'success_message' => 'Heslo bylo úspěšně změněno',
    ],

    // Email
    'email' => [
        'title' => 'Email',
        'email' => 'Email',
        'change' => 'Změnit email',
        'changed_title' => 'Email změněn',
        'verification_sent' => 'Na váš nový email byla odeslána zpráva s potvrzovacím odkazem. Pro dokončení změny emailu prosím klikněte na odkaz v této zprávě.',
        'not_verified_title' => 'Email není ověřen',
        'verification_needed' => 'Pro plné využití účtu prosím ověřte svůj email.',
        'resend_verification' => 'Znovu zaslat ověření',
        'success_message' => 'Email byl úspěšně změněn',
        'verification_resent' => 'Ověřovací email byl znovu odeslán',
    ],

    // Notifikace
    'notifications' => [
        'title' => 'Nastavení notifikací',
        'email' => 'Emailové notifikace',
        'push' => 'Push notifikace v prohlížeči',
        'newsletter' => 'Odebírat newsletter',
        'success_message' => 'Nastavení notifikací bylo úspěšně uloženo',
        'error_message' => 'Nepodařilo se uložit nastavení notifikací',
    ],

    // Zabezpečení
    'security' => [
        'title' => 'Zabezpečení účtu',
        'two_factor' => 'Dvoufaktorové ověření',
        'login_notifications' => 'Upozornění na nové přihlášení',
        'login_history' => 'Historie přihlášení',
        'no_login_records' => 'Zatím nejsou k dispozici žádné záznamy o přihlášení.',
        'suspicious' => 'Podezřelé',
        'active_sessions' => 'Aktivní relace',
        'logout_other_devices' => 'Odhlásit ostatní zařízení',
        'logout_others_success' => 'Ostatní zařízení byla úspěšně odhlášena',
        'logout_others_error' => 'Při odhlašování ostatních zařízení došlo k chybě',
        'login_notifications_saved' => 'Nastavení notifikací o přihlášení bylo uloženo',
        'save_error' => 'Nepodařilo se uložit nastavení',
        'history_load_error' => 'Nepodařilo se načíst historii přihlášení',
        'two_factor_enable' => 'Aktivovat dvoufaktorové ověření',
        'two_factor_disable' => 'Deaktivovat dvoufaktorové ověření',
        'two_factor_enable_info' => 'Pro aktivaci dvoufaktorového ověření naskenujte následující QR kód pomocí autentifikační aplikace (např. Google Authenticator, Authy nebo Microsoft Authenticator):',
        'two_factor_disable_warning' => 'Opravdu chcete deaktivovat dvoufaktorové ověření? Tím se sníží bezpečnost vašeho účtu.',
        'generating_qr' => 'Generuji QR kód...',
        'qr_code_alt' => 'QR kód pro dvoufaktorové ověření',
        'scan_qr_or_url' => 'Naskenujte QR kód nebo zadejte URL do vaší autentifikační aplikace:',
        'copy_url' => 'Zkopírovat URL',
        'secret_key' => 'Tajný klíč pro ruční zadání:',
        'copy_key' => 'Zkopírovat klíč',
        'verification_code' => 'Ověřovací kód',
        'code_required' => 'Zadejte ověřovací kód',
        'code_format' => 'Kód musí obsahovat 6 číslic',
        'remember_device' => 'Zapamatovat toto zařízení',
        'verify_code' => 'Ověřit kód',
        'disable' => 'Deaktivovat',
    ],

    // Nastavení
    'settings' => [
        'title' => 'Nastavení účtu',
        'language' => 'Jazyk rozhraní',
        'languages' => [
            'cs' => 'Čeština',
            'en' => 'English',
        ],
        'theme' => 'Vzhled aplikace',
        'themes' => [
            'light' => 'Světlý',
            'dark' => 'Tmavý',
            'system' => 'Podle systému',
        ],
        'theme_preview' => 'Ukázka zvoleného vzhledu',
        'theme_light' => 'Světlý režim',
        'theme_dark' => 'Tmavý režim',
        'theme_system' => 'Podle systému (:mode)',
        'collections' => [
            'title' => 'Kolekce',
            'auto_save_to_default' => 'Automaticky ukládat do výchozí kolekce',
            'auto_save_to_default_hint' => 'Pokud je zapnuto, karty z katalogu se budou automaticky ukládat do vaší výchozí kolekce. Jinak budete moci vybrat kolekci ručně.',
        ],
        'colors' => [
            'primary' => 'Primární',
            'secondary' => 'Sekundární',
            'accent' => 'Akcent',
            'error' => 'Chyba',
        ],
        'save' => 'Uložit',
        'success_message' => 'Nastavení bylo úspěšně uloženo',
        'error_message' => 'Při ukládání nastavení došlo k chybě',
    ],

    // Smazání účtu
    'delete' => [
        'title' => 'Smazání účtu',
        'warning_title' => 'Nevratná akce',
        'warning_text' => 'Smazání účtu je nevratná akce. Všechna vaše data budou trvale odstraněna.',
        'password_confirm' => 'Pro potvrzení zadejte své heslo',
        'understand' => 'Rozumím, že tato akce je nevratná',
        'confirm_button' => 'Smazat účet',
    ],
]; 