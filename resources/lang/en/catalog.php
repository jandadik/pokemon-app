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
        'active' => 'Active filters:',
        'search' => 'Search',
        'series' => 'Series',
        'type' => 'Type',
        'rarity' => 'Rarity',
        'sort' => 'Sort',
        'sort_options' => [
            'release_date_desc' => 'Release date (newest)',
            'release_date_asc' => 'Release date (oldest)',
            'name_asc' => 'Name (A-Z)',
            'name_desc' => 'Name (Z-A)',
            'price_asc' => 'Price (lowest)',
            'price_desc' => 'Price (highest)',
        ],
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
    ],
    
    // Cards
    'cards' => [
        'title' => 'Cards',
        'search' => 'Search card',
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
    ],
    
    // Card types
    'types' => [
        'pokemon' => 'Pokémon',
        'trainer' => 'Trainer',
        'energy' => 'Energy',
        'all' => 'All types',
    ],
    
    // Rarities
    'rarities' => [
        'common' => 'Common',
        'uncommon' => 'Uncommon',
        'rare' => 'Rare',
        'rare_holo' => 'Rare Holo',
        'ultra_rare' => 'Ultra Rare',
        'secret_rare' => 'Secret Rare',
        'all' => 'All rarities',
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