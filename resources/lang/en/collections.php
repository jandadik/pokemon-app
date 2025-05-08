<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Collections Language Lines
    |--------------------------------------------------------------------------
    |
    | Translations for the user collections management section.
    |
    */

    'title' => 'My Collections',
    
    'default_badge' => 'Default',
    'public_badge' => 'Public',
    
    'buttons' => [
        'create' => 'Create Collection',
        'create_first' => 'Create Your First Collection',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'add_card' => 'Add Card',
        'add_first_card' => 'Add Your First Card',
        'back_to_collection' => 'Back to Collection',
        'export' => 'Export',
        'print' => 'Print',
        'set_default' => 'Set as Default',
        'sort_asc' => 'Sort Ascending',
        'sort_desc' => 'Sort Descending',
        'grid_view' => 'Grid View',
        'list_view' => 'List View',
    ],
    
    'table' => [
        'name' => 'Name',
        'cards' => 'Cards',
        'public' => 'Public',
        'default' => 'Default',
        'actions' => 'Actions',
    ],
    
    'empty' => [
        'title' => 'No Collections Yet',
        'text' => 'You haven\'t created any collections yet. Start by creating your first collection!',
        'no_description' => 'No description',
    ],
    
    'create' => [
        'title' => 'Create New Collection',
    ],
    
    'edit' => [
        'title' => 'Edit Collection',
        'form_title' => 'Collection Details',
        'info_title' => 'Information',
        'created_at' => 'Created At',
        'updated_at' => 'Last Updated',
    ],
    
    'delete' => [
        'title' => 'Delete Collection',
        'confirmation' => 'Are you sure you want to delete collection ":name"? This action cannot be undone.',
        'default_warning' => 'This is your default collection. If you delete it, another collection will be set as default.',
    ],
    
    'form' => [
        'name' => 'Collection Name',
        'description' => 'Description',
        'is_public' => 'Make Collection Public',
        'is_public_hint' => 'Public collections can be viewed by other users',
        'is_default' => 'Set as Default Collection',
        'is_default_hint' => 'Default collection is used when adding cards without specifying a collection',
    ],
    
    'danger_zone' => [
        'title' => 'Danger Zone',
        'delete_warning' => 'Deleting this collection will permanently remove it and all its contents. This action cannot be undone.',
    ],
    
    'cards' => [
        'title' => 'Cards in Collection',
        'empty' => [
            'title' => 'No Cards Yet',
            'text' => 'This collection doesn\'t have any cards yet. Start by adding your first card!',
        ],
        'table' => [
            'name' => 'Card Name',
            'set' => 'Set',
            'condition' => 'Condition',
            'quantity' => 'Quantity',
            'actions' => 'Actions',
        ],
    ],
    
    'stats' => [
        'title' => 'Collection Statistics',
        'total_cards' => 'Total Cards',
        'unique_cards' => 'Unique Cards',
        'total_value' => 'Total Value',
    ],
    
    'filters' => [
        'title' => 'Filter Cards',
        'search' => 'Search',
        'rarity' => 'Rarity',
        'condition' => 'Condition',
        'reset' => 'Reset Filters',
    ],
    
    'condition' => [
        'mint' => 'Mint',
        'near_mint' => 'Near Mint',
        'excellent' => 'Excellent',
        'good' => 'Good',
        'played' => 'Played',
        'poor' => 'Poor',
    ],
]; 