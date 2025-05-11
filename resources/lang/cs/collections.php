<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Collections Language Lines
    |--------------------------------------------------------------------------
    |
    | Překlady pro sekci správy uživatelských sbírek.
    |
    */

    'title' => 'Moje sbírky',
    'collections_count_text' => '{1} kolekce|[2,4] kolekce|[5,*] kolekcí',

    'titles' => [
        'index' => 'Moje sbírky',
        'create' => 'Vytvořit novou sbírku',
        'edit' => 'Upravit sbírku',
        'detail' => 'Detail sbírky',
    ],

    'descriptions' => [
        'create_new_collection' => 'Zadejte podrobnosti pro vaši novou sbírku.',
        'edit_collection' => 'Upravte detaily vaší sbírky.',
    ],
    
    'default_badge' => 'Výchozí',
    'public_badge' => 'Veřejná',
    
    'buttons' => [
        'create' => 'Vytvořit sbírku',
        'create_first' => 'Vytvořit první sbírku',
        'save' => 'Uložit',
        'cancel' => 'Zrušit',
        'delete' => 'Smazat',
        'edit' => 'Upravit',
        'add_card' => 'Přidat kartu',
        'add_first_card' => 'Přidat první kartu',
        'back_to_collection' => 'Zpět na sbírku',
        'export' => 'Exportovat',
        'print' => 'Tisknout',
        'set_default' => 'Nastavit jako výchozí',
        'sort_asc' => 'Seřadit vzestupně',
        'sort_desc' => 'Seřadit sestupně',
        'grid_view' => 'Zobrazit jako mřížku',
        'list_view' => 'Zobrazit jako seznam',
        'unset_default' => 'Zrušit jako výchozí',
        'toggle_visibility' => 'Přepnout viditelnost',
        'show' => 'Detail',
    ],
    
    'table' => [
        'name' => 'Název',
        'cards' => 'Karty',
        'public' => 'Veřejná',
        'default' => 'Výchozí',
        'actions' => 'Akce',
    ],
    
    'fields' => [
        'name' => 'Název',
        'description' => 'Popis',
        'is_default' => 'Výchozí',
        'visibility' => 'Viditelnost',
        'actions' => 'Akce',
        'name_label' => 'Název sbírky',
        'name_placeholder' => 'Např. Moje oblíbené karty',
        'description_label' => 'Popis (volitelný)',
        'description_placeholder' => 'Krátký popis obsahu nebo účelu sbírky',
        'is_public_label' => 'Veřejná sbírka',
    ],
    
    'empty' => [
        'title' => 'Zatím žádné sbírky',
        'text' => 'Zatím jste nevytvořili žádnou sbírku. Začněte vytvořením své první sbírky!',
        'no_description' => 'Bez popisu',
    ],
    
    'edit' => [
        'title' => 'Upravit sbírku',
        'form_title' => 'Údaje o sbírce',
        'info_title' => 'Informace',
        'created_at' => 'Vytvořeno',
        'updated_at' => 'Poslední aktualizace',
    ],
    
    'delete' => [
        'title' => 'Smazat sbírku',
        'confirmation' => 'Opravdu chcete smazat sbírku ":name"? Tuto akci nelze vrátit zpět.',
        'default_warning' => 'Toto je vaše výchozí sbírka. Pokud ji smažete, bude jako výchozí nastavena jiná sbírka.',
    ],
    
    'form' => [
        'name' => 'Název sbírky',
        'description' => 'Popis',
        'is_public' => 'Zveřejnit sbírku',
        'is_public_hint' => 'Veřejné sbírky mohou prohlížet ostatní uživatelé',
        'is_default' => 'Nastavit jako výchozí sbírku',
        'is_default_hint' => 'Výchozí sbírka se používá při přidávání karet bez specifikace sbírky',
    ],
    
    'visibility_values' => [
        'public' => 'Veřejná',
        'private' => 'Soukromá',
    ],
    
    'danger_zone' => [
        'title' => 'Nebezpečná zóna',
        'delete_warning' => 'Smazáním této sbírky trvale odstraníte ji a veškerý její obsah. Tuto akci nelze vrátit zpět.',
    ],
    
    'cards' => [
        'title' => 'Karty ve sbírce',
        'empty' => [
            'title' => 'Zatím žádné karty',
            'text' => 'Tato sbírka zatím neobsahuje žádné karty. Začněte přidáním své první karty!',
        ],
        'table' => [
            'name' => 'Název karty',
            'set' => 'Set',
            'condition' => 'Stav',
            'quantity' => 'Množství',
            'actions' => 'Akce',
        ],
    ],
    
    'stats' => [
        'title' => 'Statistiky sbírky',
        'total_cards' => 'Celkem karet',
        'unique_cards' => 'Unikátní karty',
        'total_value' => 'Celková hodnota',
    ],
    
    'filters' => [
        'title' => 'Filtrovat karty',
        'search' => 'Hledat',
        'rarity' => 'Rarita',
        'condition' => 'Stav',
        'reset' => 'Resetovat filtry',
    ],
    
    'condition' => [
        'mint' => 'Mint',
        'near_mint' => 'Near Mint',
        'excellent' => 'Excellent',
        'good' => 'Good',
        'played' => 'Played',
        'poor' => 'Poor',
    ],

    'messages' => [
        'deleted_successfully' => 'Sbírka byla úspěšně smazána.',
        'delete_failed' => 'Nepodařilo se smazat sbírku.',
        'default_updated_successfully' => 'Výchozí stav sbírky byl úspěšně aktualizován.',
        'update_default_failed' => 'Nepodařilo se aktualizovat výchozí stav.',
        'visibility_updated_successfully' => 'Viditelnost sbírky byla úspěšně aktualizována.',
        'update_visibility_failed' => 'Nepodařilo se aktualizovat viditelnost.',
        'created_successfully' => 'Sbírka byla úspěšně vytvořena.',
        'creation_failed' => 'Při vytváření sbírky došlo k chybě.',
        'updated_successfully' => 'Sbírka byla úspěšně aktualizována.',
        'update_failed' => 'Při aktualizaci sbírky došlo k chybě.',
        'toggle_default_unauthorized' => 'Nemáte oprávnění k této akci.',
        'toggle_default_failed' => 'Nepodařilo se aktualizovat výchozí stav sbírky.',
        'toggle_visibility_failed' => 'Nepodařilo se aktualizovat viditelnost sbírky.',
    ],

    'actions' => [
        'set_as_default' => 'Nastavit jako výchozí',
        'make_public' => 'Zveřejnit sbírku',
        'make_private' => 'Zneveřejnit sbírku',
    ],

    'cards_in_collection' => 'Karty ve sbírce',
    'no_cards_in_collection' => 'Tato sbírka zatím neobsahuje žádné karty.',
    'cards_list_placeholder' => 'Seznam karet ve sbírce bude zobrazen zde.',
    'no_description' => 'Bez popisu',

    'delete_dialog' => [
        'title' => 'Smazat sbírku',
        'message' => 'Opravdu chcete smazat sbírku "{name}"? Tato akce nelze vrátit zpět a všechny karty ve sbírce budou odstraněny ze sbírky.',
    ],

    'dialogs' => [
        'delete' => [
            'title' => 'Potvrdit smazání sbírky'
        ]
    ],
]; 