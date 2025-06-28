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
    'subtitle' => 'Manage your {count} |{1} collection|[*,*] collections',
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
        'duplicate' => 'Duplicate',
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
        'manage_items' => 'Manage Items',
        'make_private' => 'Make Private',
        'make_public' => 'Make Public',
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
        'title' => [
            'create' => 'Add Item to Collection',
            'edit' => 'Edit Collection Item',
        ],
        'condition' => 'Card Condition',
        'language' => 'Language',
        'quantity' => 'Quantity',
        'purchase_price' => 'Purchase Price',
        'grading' => 'Grading Agency',
        'grading_cert' => 'Certificate Number',
        'grading_cert_placeholder' => 'Enter certificate number',
        'first_edition' => 'First Edition',
        'location' => 'Location (box, folder...)',
        'note' => 'Note',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'errors' => [
            'required' => 'This field is required.',
            'quantity' => 'Quantity must be between 1 and 999.',
            'price' => 'Price must be a number.',
            'location' => 'Maximum length is 100 characters.',
            'note' => 'Maximum length is 500 characters.',
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
            'en' => 'English',
            'de' => 'German',
            'fr' => 'French',
            'cs' => 'Czech',
            'jp' => 'Japanese',
            'english' => 'English',
            'german' => 'German',
            'french' => 'French',
            'czech' => 'Czech',
            'japanese' => 'Japanese',
            'spanish' => 'Spanish',
            'italian' => 'Italian',
            'portuguese' => 'Portuguese',
            'chinese' => 'Chinese',
            'korean' => 'Korean',
        ],
        'grading_agencies' => [
            'psa' => 'PSA',
            'bgs' => 'BGS',
            'cgc' => 'CGC',
            'sgc' => 'SGC',
        ],
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
            'market_price' => 'Market Price',
            'actions' => 'Actions',
        ],
        'set' => 'Set',
        'basic_info' => 'Basic Information',
        'number' => 'Card Number',
        'condition_quantity' => 'Condition & Quantity',
        'price_info' => 'Price Information',
        'purchase_price' => 'Purchase Price',
        'market_value' => 'Market Value',
        'profit_loss' => 'Profit/Loss',
        'metadata' => 'Metadata',
        'date_added' => 'Date Added',
        'last_updated' => 'Last Updated',
        'variant_type' => 'Variant Type',
    ],
    
    'stats' => [
        'title' => 'Collection Statistics',
        'total_cards' => 'Total Cards',
        'unique_cards' => 'Unique Cards',
        'purchase_value' => 'Purchase Value',
        'market_value' => 'Market Value',
        'total_value' => 'Total Value', // Kept for backward compatibility
    ],
    
    'filters' => [
        'title' => 'Filter Cards',
        'search' => 'Search',
        'rarity' => 'Rarity',
        'condition' => 'Condition',
        'reset' => 'Reset Filters',
        'all_rarities' => 'All rarities',
        'all_languages' => 'All languages',
        'all_conditions' => 'All conditions',
        'sort' => 'Sort by',
        'sort_name' => 'Name',
        'sort_number' => 'Number',
        'sort_rarity' => 'Rarity',
        'sort_price' => 'Price',
        'sort_created_at' => 'Date added',
        'per_page' => 'Per page',
        'no_results_title' => 'No Results',
        'no_results_text' => 'No cards found for the current filters. Try adjusting your search criteria.',
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
        'message' => 'Are you sure you want to delete collection "{name}"?',
        'warning' => 'This action is irreversible. All cards in this collection will be permanently deleted.',
    ],
    
    'dialogs' => [
        'delete' => [
            'title' => 'Confirm Collection Deletion'
        ]
    ],

    'variant_selection' => [
        'title' => 'Card Variant Selection',
        'no_variants' => 'No variants available for this card.',
        'loading' => 'Loading available variants...',
        'select_variant' => 'Select card variant',
        'selected_card' => 'Selected card',
        'default_variant' => 'Default variant',
    ],

    // New section for collection items
    'items' => [
        'empty_title' => 'No Cards Yet',
        'empty_text' => 'This collection doesn\'t have any cards yet. Start by adding your first card!',
        'add_new_item' => 'Add Card',
        'add_first_card' => 'Add First Card',
        'create_title' => 'Add Item to Collection',
        'create_description' => 'Select a card, its variant, and enter the required details.',
        'select_card' => 'Card Selection',
        'select_card_variant' => 'Card and Variant Selection',
        'selected_card_variant' => 'Selected Card and Variant',
        'card_search_instructions' => 'Search for a card by name, code, or number. Minimum 2 characters.',
        'change_selection' => 'Change selection',
        'demo_selection_notice' => 'A demo card/variant is shown below. In the future, a full selection mechanism will be here.',
        'edit_title' => 'Edit Collection Item',
        'edit_description' => 'Update the details of the selected item in your collection.',
        'editing_item_info' => 'Editing Item Information',
        'no_item_data_for_edit' => 'Item data not found for editing.',
        'index_title' => 'Items in Collection: :name',
        'index_description' => 'Manage the individual cards and items within your collection.',
        'table_header_card' => 'Card',
        'table_header_variant' => 'Variant',
        'table_header_condition' => 'Condition',
        'table_header_language' => 'Language',
        'table_header_quantity' => 'Quantity',
        'table_header_actions' => 'Actions',
        'title_singular' => 'item',
        'messages' => [
            'created' => 'Item was successfully added to the collection.',
            'updated' => 'Item was successfully updated.',
            'deleted' => 'Item was successfully removed from the collection.',
        ],
    ],
]; 