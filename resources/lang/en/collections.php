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
    'collections_count_text' => '{1} collection|[*,*] collections',

    'titles' => [
        'index' => 'My Collections',
        'create' => 'Create New Collection',
        'edit' => 'Edit Collection',
        'detail' => 'Collection Details',
    ],

    'descriptions' => [
        'create_new_collection' => 'Enter the details for your new collection.',
        'edit_collection' => 'Update the details of your collection.',
    ],
    
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
        'unset_default' => 'Unset as Default',
        'toggle_visibility' => 'Toggle Visibility',
        'show' => 'Show',
    ],
    
    'table' => [
        'name' => 'Name',
        'cards' => 'Cards',
        'public' => 'Public',
        'default' => 'Default',
        'actions' => 'Actions',
    ],

    'fields' => [
        'name' => 'Name', 
        'description' => 'Description', 
        'is_default' => 'Default', 
        'visibility' => 'Visibility',
        'actions' => 'Actions',

        'name_label' => 'Collection Name',
        'name_placeholder' => 'E.g., My Favorite Cards',
        'description_label' => 'Description (optional)',
        'description_placeholder' => 'A short description of the collection\'s content or purpose',
        'is_public_label' => 'Public Collection',
    ],
    
    'empty' => [
        'title' => 'No Collections Yet',
        'text' => 'You haven\'t created any collections yet. Start by creating your first collection!',
        'no_description' => 'No description',
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

    'visibility_values' => [
        'public' => 'Public',
        'private' => 'Private',
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

    'messages' => [
        'deleted_successfully' => 'Collection deleted successfully.',
        'delete_failed' => 'Failed to delete collection.',
        'default_updated_successfully' => 'Default status of the collection was successfully updated.',
        'update_default_failed' => 'Failed to update default status.',
        'visibility_updated_successfully' => 'Visibility of the collection was successfully updated.',
        'update_visibility_failed' => 'Failed to update visibility.',

        'created_successfully' => 'Collection was successfully created.',
        'creation_failed'      => 'Failed to create collection.',
        'updated_successfully' => 'Collection was successfully updated.',
        'update_failed'        => 'Failed to update collection.',
        'toggle_default_unauthorized' => 'You are not authorized to perform this action.',
        'toggle_default_failed'       => 'Failed to update default status of the collection.',
        'toggle_visibility_failed'    => 'Failed to update visibility of the collection.',
    ],
    
    'actions' => [
        'set_as_default' => 'Set as Default',
        'make_public' => 'Make Public',
        'make_private' => 'Make Private',
    ],

    'cards_in_collection' => 'Cards in Collection',
    'no_cards_in_collection' => 'This collection doesn\'t have any cards yet.',
    'cards_list_placeholder' => 'List of cards in the collection will be displayed here.',
    'no_description' => 'No description',

    'delete_dialog' => [
        'title' => 'Delete Collection',
        'message' => 'Are you sure you want to delete collection "{name}"? This action cannot be undone and all cards in the collection will be removed from the collection.',
    ],
    
    'dialogs' => [
        'delete' => [
            'title' => 'Confirm Collection Deletion'
        ]
    ],
]; 