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
    'subtitle' => 'Spravujte své {count} |{1} sbírku|[2,4] sbírky|[5,*] sbírek',
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
        'duplicate' => 'Duplikovat',
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
        'manage_items' => 'Spravovat položky',
        'make_private' => 'Změnit na soukromou',
        'make_public' => 'Změnit na veřejnou',
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
    
    'delete_dialog' => [
        'title' => 'Potvrdit smazání sbírky',
        'message' => 'Opravdu chcete smazat sbírku ":name"?',
        'warning' => 'Tato akce je nevratná. Všechny karty v této sbírce budou trvale smazány.',
    ],
    
    'form' => [
        'name' => 'Název sbírky',
        'description' => 'Popis',
        'is_public' => 'Zveřejnit sbírku',
        'is_public_hint' => 'Veřejné sbírky mohou prohlížet ostatní uživatelé',
        'is_default' => 'Nastavit jako výchozí sbírku',
        'is_default_hint' => 'Výchozí sbírka se používá při přidávání karet bez specifikace sbírky',
        'title' => [
            'create' => 'Přidat položku do sbírky',
            'edit' => 'Upravit položku sbírky',
        ],
        'condition' => 'Stav karty',
        'language' => 'Jazyk',
        'quantity' => 'Počet kusů',
        'purchase_price' => 'Nákupní cena',
        'grading' => 'Grading agentura',
        'grading_cert' => 'Číslo certifikátu',
        'first_edition' => 'První edice',
        'location' => 'Umístění (box, složka...)',
        'note' => 'Poznámka',
        'save' => 'Uložit',
        'cancel' => 'Zrušit',
        'errors' => [
            'required' => 'Toto pole je povinné.',
            'quantity' => 'Počet musí být v rozmezí 1–999.',
            'price' => 'Cena musí být číslo.',
            'location' => 'Maximální délka je 100 znaků.',
            'note' => 'Maximální délka je 500 znaků.',
        ],
        'conditions' => [
            'nm' => 'Near Mint',
            'ex' => 'Excellent',
            'gd' => 'Good',
            'pl' => 'Played',
            'po' => 'Poor',
            'mint' => 'Mint',
            'near_mint' => 'Near Mint',
            'excellent' => 'Excellent',
            'good' => 'Good',
            'played' => 'Played',
            'poor' => 'Poor',
        ],
        'languages' => [
            'en' => 'Anglicky',
            'de' => 'Německy',
            'fr' => 'Francouzsky',
            'cs' => 'Česky',
            'jp' => 'Japonsky',
            'english' => 'Anglicky',
            'german' => 'Německy',
            'french' => 'Francouzsky',
            'czech' => 'Česky',
            'japanese' => 'Japonsky',
            'spanish' => 'Španělsky',
            'italian' => 'Italsky',
            'portuguese' => 'Portugalsky',
            'chinese' => 'Čínsky',
            'korean' => 'Korejsky',
        ],
        'grading_agencies' => [
            'psa' => 'PSA',
            'bgs' => 'BGS',
            'cgc' => 'CGC',
            'sgc' => 'SGC',
        ],
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
            'market_price' => 'Tržní cena',
            'actions' => 'Akce',
        ],
        'set' => 'Set',
        'basic_info' => 'Základní informace',
        'number' => 'Číslo karty',
        'condition_quantity' => 'Stav a množství',
        'price_info' => 'Cenové informace',
        'purchase_price' => 'Nákupní cena',
        'market_value' => 'Tržní hodnota',
        'profit_loss' => 'Zisk/Ztráta',
        'metadata' => 'Metadata',
        'date_added' => 'Datum přidání',
        'last_updated' => 'Poslední aktualizace',
        'variant_type' => 'Typ varianty',
    ],
    
    'stats' => [
        'title' => 'Statistiky sbírky',
        'total_cards' => 'Celkem karet',
        'unique_cards' => 'Unikátní karty',
        'purchase_value' => 'Pořizovací hodnota',
        'market_value' => 'Tržní hodnota',
        'total_value' => 'Celková hodnota', // Zachováno pro zpětnou kompatibilitu
    ],
    
    'filters' => [
        'title' => 'Filtrovat karty',
        'search' => 'Hledat',
        'rarity' => 'Rarita',
        'condition' => 'Stav',
        'reset' => 'Resetovat filtry',
        'all_rarities' => 'Všechny vzácnosti',
        'all_languages' => 'Všechny jazyky',
        'all_conditions' => 'Všechny stavy',
        'sort' => 'Řadit podle',
        'sort_name' => 'Název',
        'sort_number' => 'Číslo',
        'sort_rarity' => 'Rarita',
        'sort_price' => 'Cena',
        'sort_created_at' => 'Datum přidání',
        'per_page' => 'Na stránku',
        'no_results_title' => 'Žádné výsledky',
        'no_results_text' => 'Pro aktuální filtry nebyly nalezeny žádné karty. Zkuste upravit kritéria vyhledávání.',
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

    'dialogs' => [
        'delete' => [
            'title' => 'Potvrdit smazání sbírky'
        ]
    ],

    'variant_selection' => [
        'title' => 'Výběr varianty karty',
        'no_variants' => 'Pro tuto kartu nejsou dostupné žádné varianty.',
        'loading' => 'Načítám dostupné varianty...',
        'select_variant' => 'Vyberte variantu karty',
        'selected_card' => 'Vybraná karta',
        'default_variant' => 'Standardní varianta',
    ],

    'items' => [
        'empty_title' => 'Zatím žádné karty',
        'empty_text' => 'Tato sbírka zatím neobsahuje žádné karty. Začněte přidáním své první karty!',
        'add_new_item' => 'Přidat kartu',
        'messages' => [
            'created' => 'Položka byla úspěšně přidána do sbírky.',
            'updated' => 'Položka byla úspěšně aktualizována.',
            'deleted' => 'Položka byla úspěšně odstraněna ze sbírky.',
            'bulk_deleted' => ':count položek bylo úspěšně smazáno.',
            'bulk_duplicated' => ':count položek bylo úspěšně duplikováno.',
            'bulk_updated' => ':count položek bylo úspěšně aktualizováno.',
        ],
        'add_first_card' => 'Přidat první kartu',
        'create_title' => 'Přidat položku do sbírky',
        'create_description' => 'Vyberte kartu, její variantu a zadejte požadované detaily.',
        'select_card' => 'Výběr karty',
        'select_card_variant' => 'Výběr karty a varianty',
        'selected_card_variant' => 'Vybraná karta a varianta',
        'card_search_instructions' => 'Vyhledejte kartu podle názvu, kódu nebo čísla. Minimálně 2 znaky.',
        'change_selection' => 'Změnit výběr',
        'demo_selection_notice' => 'Níže je zobrazena demo karta/varianta. V budoucnu zde bude plnohodnotný výběr.',
        'edit_title' => 'Upravit položku sbírky',
        'edit_description' => 'Upravte detaily vybrané položky ve vaší sbírce.',
        'editing_item_info' => 'Informace o editované položce',
        'no_item_data_for_edit' => 'Pro editaci nebyla nalezena data položky.',
        'index_title' => 'Položky ve sbírce: :name',
        'index_description' => 'Správa jednotlivých karet a položek ve vaší sbírce.',
        'table_header_card' => 'Karta',
        'table_header_variant' => 'Varianta',
        'table_header_condition' => 'Stav',
        'table_header_language' => 'Jazyk',
        'table_header_quantity' => 'Počet',
        'table_header_actions' => 'Akce',
        'title_singular' => 'položku',
        'messages' => [
            'created' => 'Položka byla úspěšně přidána do sbírky.',
            'updated' => 'Položka byla úspěšně aktualizována.',
            'deleted' => 'Položka byla úspěšně odstraněna ze sbírky.',
        ],
    ],
]; 