<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Catalog Translations
    |--------------------------------------------------------------------------
    |
    | Language lines for the catalog section of the application with Pokémon cards and sets.
    |
    */

    // General
    'title' => 'Pokémon Cards Catalog',
    'no_data' => 'No data to display',
    'loading' => 'Loading...',
    'error' => 'An error occurred while loading data',
    
    // Filters and sorting
    'filters' => [
        'title' => 'Filters',
        'apply' => 'Apply filters',
        'reset' => 'Reset filters',
        'active_filters' => 'Active filters:',
        'search' => 'Search',
        'series' => 'Series',
        'type' => 'Type',
        'rarity' => 'Rarity',
        'set' => 'Set',
        'per_page' => 'Per page',
        'sort' => 'Sort',
        'all_types' => 'All types',
        'all_rarities' => 'All rarities',
        'all_sets' => 'All sets',
        'per_page_options' => [
            '30' => '30',
            '60' => '60',
            '120' => '120',
        ],
    ],
    
    // New section for sorting
    'sorting' => [
        'number_asc' => 'Number (ascending)',
        'number_desc' => 'Number (descending)',
        'name_asc' => 'Name (A-Z)',
        'name_desc' => 'Name (Z-A)',
        'price_asc' => 'Price (lowest)',
        'price_desc' => 'Price (highest)',
        'rarity_asc' => 'Rarity (lowest)',
        'rarity_desc' => 'Rarity (highest)',
        'release_date_desc' => 'Release date (newest)',
        'release_date_asc' => 'Release date (oldest)',
    ],
    
    // Sets
    'sets' => [
        'title' => 'Card Sets',
        'search' => 'Search set',
        'no_sets' => 'No card sets found',
        'cards_count' => 'cards',
        'release_date' => 'Released',
        'details' => 'Details',
        'view_set' => 'View set',
        'market_price' => 'Market price',
        'market_price_tooltip' => 'Total market price of the set',
        'all' => 'All sets',
    ],
    
    // Cards
    'cards' => [
        'title' => 'Cards',
        'all_cards' => 'All Cards',
        'search' => 'Search',
        'no_cards' => 'No cards found',
        'view_card' => 'View card',
        'card_details' => 'Card details',
        'card_name' => 'Card name',
        'card_type' => 'Type',
        'card_rarity' => 'Rarity',
        'card_price' => 'Price',
        'collection' => 'Collection',
        'collector_number' => 'Number',
        'add_to_collection' => 'Add to collection',
        'remove_from_collection' => 'Remove from collection',
        'set' => 'Set',
        'name' => 'Card name',
        'type' => 'Type',
        'rarity' => 'Rarity',
        'price' => 'Price',
        'number' => 'Number',
    ],
    
    // Card types
    'types' => [
        'Pokémon' => 'Pokémon',
        'Trainer' => 'Trainer',
        'Energy' => 'Energy',
        'pokemon' => 'Pokémon',
        'trainer' => 'Trainer',
        'energy' => 'Energy',
        'all' => 'All types',
    ],
    
    // Rarities
    'rarities' => [
        'Common' => 'Common',
        'Uncommon' => 'Uncommon',
        'Rare' => 'Rare',
        'RareHolo' => 'Rare Holo',
        'RareUltra' => 'Rare Ultra',
        'RareSecret' => 'Rare Secret',
        'AmazingRare' => 'Amazing Rare',
        'UltraRare' => 'Ultra Rare',
        'SecretRare' => 'Secret Rare',
        'Promo' => 'Promo',
        'common' => 'Common',
        'uncommon' => 'Uncommon',
        'rare' => 'Rare',
        'rare_holo' => 'Rare Holo',
        'ultra_rare' => 'Ultra Rare',
        'secret_rare' => 'Secret Rare',
        'all' => 'All rarities',
    ],
    
    // New section for table headers
    'table' => [
        'headers' => [
            'image' => '',
            'name' => 'Card name',
            'set' => 'Set',
            'number' => 'Number',
            'rarity' => 'Rarity',
            'price_avg30' => 'Price (Avg30)',
        ]
    ],
    
    // Pagination
    'pagination' => [
        'previous' => 'Previous',
        'next' => 'Next',
        'showing' => 'Showing',
        'to' => 'to',
        'of' => 'of',
        'results' => 'results',
        'per_page' => 'Per page:',
    ],
]; 