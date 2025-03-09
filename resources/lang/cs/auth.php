<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Autentizační překlady
    |--------------------------------------------------------------------------
    |
    | Následující jazykové řádky se používají při autentizaci
    | pro různé zprávy, které potřebujeme zobrazit uživateli.
    |
    */

    'failed' => 'Tyto přihlašovací údaje neodpovídají našim záznamům.',
    'throttle' => 'Příliš mnoho pokusů o přihlášení. Zkuste to prosím znovu za :seconds sekund.',
    'password' => 'Zadané heslo je nesprávné.',
    'not_verified' => 'Váš e-mail nebyl ověřen.',
    
    'login' => [
        'title' => 'Přihlášení',
        'email' => 'E-mail',
        'password' => 'Heslo',
        'remember' => 'Zapamatovat si mě',
        'submit' => 'Přihlásit se',
        'forgot' => 'Zapomněli jste heslo?',
        'no_account' => 'Nemáte účet?',
        'register' => 'Zaregistrujte se',
        'or' => 'nebo',
        'sso' => 'Přihlásit se přes SSO',
        'google' => 'Přihlásit se přes Google',
    ],
    
    'register' => [
        'title' => 'Registrace',
        'name' => 'Jméno',
        'email' => 'E-mail',
        'password' => 'Heslo',
        'password_confirm' => 'Potvrdit heslo',
        'submit' => 'Registrovat',
        'has_account' => 'Již máte účet?',
        'login' => 'Přihlaste se',
    ],
    
    'password_reset' => [
        'title' => 'Obnovení hesla',
        'email' => 'E-mail',
        'password' => 'Heslo',
        'password_confirm' => 'Potvrdit heslo',
        'submit' => 'Obnovit heslo',
        'back_to_login' => 'Zpět k přihlášení',
    ],
    
    'forgot_password' => [
        'title' => 'Obnovení hesla',
        'description' => 'Zapomněli jste heslo? Žádný problém. Zadejte svou e-mailovou adresu a my vám zašleme odkaz pro obnovení hesla.',
        'email' => 'E-mail',
        'submit' => 'Odeslat odkaz pro obnovení hesla',
        'back_to_login' => 'Zpět na přihlášení',
    ],
    
    'two_factor' => [
        'title' => 'Dvoufaktorové ověření',
        'subtitle' => 'Pro pokračování zadejte kód z vaší autentifikační aplikace',
        'code' => 'Ověřovací kód',
        'code_placeholder' => 'Zadejte 6místný kód',
        'submit' => 'Ověřit',
        'recovery' => 'Použít záložní kód',
        'invalid_code' => 'Nesprávný ověřovací kód. Zkuste to prosím znovu.',
        'no_access' => 'Nemáte přístup k vaší autentifikační aplikaci?',
        'contact_admin' => 'Kontaktujte administrátora pro obnovení přístupu k vašemu účtu.',
    ],
    
    'verify_email' => [
        'title' => 'Ověření emailové adresy',
        'description' => 'Děkujeme za registraci! Než začnete, mohli byste prosím ověřit svou e-mailovou adresu kliknutím na odkaz, který jsme vám právě poslali? Pokud jste e-mail neobdrželi, rádi vám pošleme další.',
        'success' => 'Nový ověřovací odkaz byl odeslán na vaši emailovou adresu.',
        'submit' => 'Znovu odeslat ověřovací email',
    ],
    
    'logout' => 'Odhlásit se',
    
    // Validační hlášky
    'validation' => [
        'email' => [
            'required' => 'E-mail je povinný',
            'valid' => 'E-mail musí být platný',
        ],
        'password' => [
            'required' => 'Heslo je povinné',
            'min_length' => 'Heslo musí mít alespoň :length znaků',
            'uppercase' => 'Heslo musí obsahovat alespoň jedno velké písmeno',
            'lowercase' => 'Heslo musí obsahovat alespoň jedno malé písmeno',
            'number' => 'Heslo musí obsahovat alespoň jednu číslici',
        ],
        'password_confirmation' => [
            'required' => 'Potvrzení hesla je povinné',
            'match' => 'Hesla se neshodují',
        ],
        'code' => [
            'required' => 'Ověřovací kód je povinný',
            'numeric' => 'Ověřovací kód musí obsahovat pouze číslice',
            'length' => 'Ověřovací kód musí mít :length číslic',
        ],
    ],
]; 