<?php

return [
    /*
    |--------------------------------------------------------------------------
    | UI Language Lines
    |--------------------------------------------------------------------------
    |
    | Následující jazykové řádky se používají v komponentách uživatelského rozhraní,
    | jako jsou menu, tlačítka, formuláře atd.
    |
    */

    // Hlavní menu a navigace - Přejmenováno s prefixem 'menu.'
    'menu' => [
        'home' => 'Domů',
        'catalog' => 'Katalog',
        'cards' => 'Karty',
        'collections' => 'Sbírky',
        'admin' => 'Admin',
    ],

    'header' => [
        'menu' => 'Menu',
        'dashboard' => 'Dashboard',
        'profile' => 'Profil',
        'account' => 'Můj účet',
        'settings' => 'Nastavení',
        'logout' => 'Odhlásit se',
        'login' => 'Přihlásit se',
        'register' => 'Registrovat',
        'search' => 'Hledat...',
        'favorites' => 'Oblíbené',
        'language' => 'Jazyk',
        'theme' => 'Téma',
    ],

    // Profil uživatele
    'profile' => [
        'title' => 'Uživatelský profil',
        'edit' => 'Upravit profil',
        'tabs' => [
            'personal' => 'Osobní údaje',
            'password' => 'Heslo',
            'email' => 'E-mail',
            'notifications' => 'Notifikace',
            'settings' => 'Nastavení',
            'security' => 'Zabezpečení',
        ],
        'personal' => [
            'name' => 'Jméno',
            'email' => 'E-mail',
            'save' => 'Uložit změny',
        ],
        'password' => [
            'current' => 'Současné heslo',
            'new' => 'Nové heslo',
            'confirm' => 'Potvrdit nové heslo',
            'update' => 'Aktualizovat heslo',
        ],
        'email' => [
            'current' => 'Současný e-mail',
            'new' => 'Nový e-mail',
            'update' => 'Aktualizovat e-mail',
            'verify' => 'Ověřit e-mail',
        ],
        'notifications' => [
            'email' => 'E-mailové notifikace',
            'push' => 'Push notifikace',
            'newsletter' => 'Odběr novinek',
            'save' => 'Uložit nastavení',
        ],
        'settings' => [
            'language' => 'Jazyk',
            'theme' => 'Téma',
            'timezone' => 'Časové pásmo',
            'save' => 'Uložit nastavení',
            'themes' => [
                'light' => 'Světlé',
                'dark' => 'Tmavé',
                'system' => 'Podle systému',
            ],
        ],
        'security' => [
            'two_factor' => 'Dvoufaktorové ověření',
            'enable' => 'Povolit',
            'disable' => 'Zakázat',
            'sessions' => 'Aktivní relace',
            'logout_others' => 'Odhlásit ostatní relace',
            'last_login' => 'Poslední přihlášení',
        ],
    ],

    // Karty a sety
    'cards' => [
        'title' => 'Karty',
        'search' => 'Hledat karty',
        'filters' => 'Filtry',
        'sort' => 'Seřadit',
        'view' => 'Zobrazit kartu',
        'not_found' => 'Žádné karty nenalezeny',
    ],
    
    'sets' => [
        'title' => 'Sety',
        'search' => 'Hledat sety',
        'filters' => 'Filtry',
        'sort' => 'Seřadit',
        'view' => 'Zobrazit set',
        'not_found' => 'Žádné sety nenalezeny',
        'cards_count' => 'Počet karet',
        'release_date' => 'Datum vydání',
    ],

    // Ostatní komponenty UI
    'pagination' => [
        'previous' => 'Předchozí',
        'next' => 'Další',
        'showing' => 'Zobrazuji',
        'to' => 'až',
        'of' => 'z',
        'results' => 'výsledků',
    ],
    
    'dialogs' => [
        'confirm' => 'Potvrdit',
        'cancel' => 'Zrušit',
        'close' => 'Zavřít',
        'save' => 'Uložit',
        'delete' => 'Smazat',
        'ok' => 'OK',
    ],
    
    // Obecné texty pro UI
    'stats' => 'Statistiky',
    'coming_soon' => 'Připravujeme pro vás',

    'buttons' => [
        'scroll_to_top' => 'Posunout nahoru',
        'create' => 'Vytvořit',
        'cancel' => 'Zrušit',
        'delete' => 'Smazat',
        'edit' => 'Upravit',
        'save' => 'Uložit',
        'submit' => 'Odeslat',
        'back' => 'Zpět',
        'close' => 'Zavřít',
        'confirm' => 'Potvrdit',
        'proceed' => 'Pokračovat',
        'yes' => 'Ano',
        'no' => 'Ne',
        'view' => 'Zobrazit',
        'download' => 'Stáhnout',
        'upload' => 'Nahrát',
        'search' => 'Hledat',
        'reset' => 'Resetovat',
    ],

    'messages' => [
        'validation_errors' => 'Opravte prosím chyby ve formuláři.',
        'data_saved' => 'Data byla úspěšně uložena.',
        'data_deleted' => 'Data byla úspěšně smazána.',
        'operation_successful' => 'Operace byla úspěšně dokončena.',
        'operation_failed' => 'Při provádění operace došlo k chybě.',
        'no_data' => 'Žádná data k zobrazení.',
        'loading' => 'Načítání...',
        'processing' => 'Zpracování...',
        'confirm_delete' => 'Opravdu chcete smazat tuto položku?',
    ],

    'labels' => [
        'search' => 'Hledat',
        'filter' => 'Filtrovat',
        'sort' => 'Seřadit',
        'from' => 'Od',
        'to' => 'Do',
        'status' => 'Stav',
        'actions' => 'Akce',
        'details' => 'Detaily',
        'settings' => 'Nastavení',
    ],

    'placeholders' => [
        'search' => 'Zadejte hledaný výraz...',
        'select' => 'Vyberte položku',
    ],

    'statuses' => [
        'active' => 'Aktivní',
        'inactive' => 'Neaktivní',
        'pending' => 'Čeká na zpracování',
        'completed' => 'Dokončeno',
        'failed' => 'Selhalo',
    ],
]; 