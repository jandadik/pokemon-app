<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Katalogové překlady
    |--------------------------------------------------------------------------
    |
    | Jazykové řádky pro katalogovou sekci aplikace s kartami a sety Pokémon.
    |
    */

    // Obecné
    'title' => 'Katalog Pokémon karet',
    'no_data' => 'Žádná data k zobrazení',
    'loading' => 'Načítání...',
    'error' => 'Došlo k chybě při načítání dat',
    
    // Filtry a řazení
    'filters' => [
        'title' => 'Filtry',
        'apply' => 'Použít filtry',
        'reset' => 'Resetovat filtry',
        'active' => 'Aktivní filtry:',
        'search' => 'Vyhledat',
        'series' => 'Série',
        'type' => 'Typ',
        'rarity' => 'Vzácnost',
        'set' => 'Set',
        'per_page' => 'Počet na stránku',
        'sort' => 'Řazení',
        'sort_options' => [
            'release_date_desc' => 'Datum vydání (nejnovější)',
            'release_date_asc' => 'Datum vydání (nejstarší)',
            'name_asc' => 'Název (A-Z)',
            'name_desc' => 'Název (Z-A)',
            'price_asc' => 'Cena (nejnižší)',
            'price_desc' => 'Cena (nejvyšší)',
            'number_asc' => 'Číslo (vzestupně)',
            'number_desc' => 'Číslo (sestupně)',
            'rarity_asc' => 'Vzácnost (nejnižší)',
            'rarity_desc' => 'Vzácnost (nejvyšší)',
        ],
    ],
    
    // Sety
    'sets' => [
        'title' => 'Sety karet',
        'search' => 'Vyhledat set',
        'no_sets' => 'Nebyly nalezeny žádné sety karet',
        'cards_count' => 'karet',
        'release_date' => 'Vydáno',
        'details' => 'Detaily',
        'view_set' => 'Zobrazit set',
        'market_price' => 'Tržní cena',
        'market_price_tooltip' => 'Celková tržní cena setu',
        'all' => 'Všechny sety',
    ],
    
    // Karty
    'cards' => [
        'title' => 'Karty',
        'all_cards' => 'Všechny karty',
        'search' => 'Vyhledat kartu',
        'no_cards' => 'Nebyly nalezeny žádné karty',
        'view_card' => 'Zobrazit kartu',
        'card_details' => 'Detail karty',
        'card_name' => 'Název karty',
        'card_type' => 'Typ',
        'card_rarity' => 'Vzácnost',
        'card_price' => 'Cena',
        'collection' => 'Sbírka',
        'collector_number' => 'Číslo',
        'add_to_collection' => 'Přidat do sbírky',
        'remove_from_collection' => 'Odebrat ze sbírky',
        'set' => 'Set',
        'name' => 'Název karty',
        'type' => 'Typ',
        'rarity' => 'Vzácnost',
        'price' => 'Cena',
        'number' => 'Číslo',
    ],
    
    // Typy karet
    'types' => [
        'pokemon' => 'Pokémon',
        'trainer' => 'Trenér',
        'energy' => 'Energie',
        'all' => 'Všechny typy',
    ],
    
    // Vzácnosti
    'rarities' => [
        'common' => 'Běžná',
        'uncommon' => 'Neobvyklá',
        'rare' => 'Vzácná',
        'rare_holo' => 'Vzácná Holo',
        'ultra_rare' => 'Ultra vzácná',
        'secret_rare' => 'Tajně vzácná',
        'all' => 'Všechny vzácnosti',
    ],
    
    // Paginace
    'pagination' => [
        'previous' => 'Předchozí',
        'next' => 'Další',
        'showing' => 'Zobrazeno',
        'to' => 'až',
        'of' => 'z',
        'results' => 'výsledků',
        'per_page' => 'Na stránku:',
    ],
]; 