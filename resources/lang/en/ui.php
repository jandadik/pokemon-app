<?php

return [
    /*
    |--------------------------------------------------------------------------
    | UI Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in user interface components,
    | such as menus, buttons, forms, etc.
    |
    */

    // Main menu and navigation - Renamed with 'menu.' prefix
    'menu' => [
        'home' => 'Home',
        'catalog' => 'Catalog',
        'cards' => 'Cards',
        'collections' => 'Collections',
        'admin' => 'Admin',
    ],

    'header' => [
        'menu' => 'Menu',
        'dashboard' => 'Dashboard',
        'profile' => 'Profile',
        'account' => 'My Account',
        'settings' => 'Settings',
        'logout' => 'Log Out',
        'login' => 'Log In',
        'register' => 'Register',
        'search' => 'Search...',
        'favorites' => 'Favorites',
        'language' => 'Language',
        'theme' => 'Theme',
    ],

    // User profile
    'profile' => [
        'title' => 'User Profile',
        'edit' => 'Edit Profile',
        'tabs' => [
            'personal' => 'Personal Information',
            'password' => 'Password',
            'email' => 'Email',
            'notifications' => 'Notifications',
            'settings' => 'Settings',
            'security' => 'Security',
        ],
        'personal' => [
            'name' => 'Name',
            'email' => 'Email',
            'save' => 'Save Changes',
        ],
        'password' => [
            'current' => 'Current Password',
            'new' => 'New Password',
            'confirm' => 'Confirm New Password',
            'update' => 'Update Password',
        ],
        'email' => [
            'current' => 'Current Email',
            'new' => 'New Email',
            'update' => 'Update Email',
            'verify' => 'Verify Email',
        ],
        'notifications' => [
            'email' => 'Email Notifications',
            'push' => 'Push Notifications',
            'newsletter' => 'Newsletter Subscription',
            'save' => 'Save Settings',
        ],
        'settings' => [
            'language' => 'Language',
            'theme' => 'Theme',
            'timezone' => 'Time Zone',
            'save' => 'Save Settings',
            'themes' => [
                'light' => 'Light',
                'dark' => 'Dark',
                'system' => 'System Default',
            ],
        ],
        'security' => [
            'two_factor' => 'Two-Factor Authentication',
            'enable' => 'Enable',
            'disable' => 'Disable',
            'sessions' => 'Active Sessions',
            'logout_others' => 'Log Out Other Sessions',
            'last_login' => 'Last Login',
        ],
    ],

    // Cards and sets
    'cards' => [
        'title' => 'Cards',
        'search' => 'Search Cards',
        'filters' => 'Filters',
        'sort' => 'Sort',
        'view' => 'View Card',
        'not_found' => 'No cards found',
    ],
    
    'sets' => [
        'title' => 'Sets',
        'search' => 'Search Sets',
        'filters' => 'Filters',
        'sort' => 'Sort',
        'view' => 'View Set',
        'not_found' => 'No sets found',
        'cards_count' => 'Cards Count',
        'release_date' => 'Release Date',
    ],

    // Other UI components
    'pagination' => [
        'previous' => 'Previous',
        'next' => 'Next',
        'showing' => 'Showing',
        'to' => 'to',
        'of' => 'of',
        'results' => 'results',
    ],
    
    'dialogs' => [
        'confirm' => 'Confirm',
        'cancel' => 'Cancel',
        'close' => 'Close',
        'save' => 'Save',
        'delete' => 'Delete',
        'ok' => 'OK',
    ],
    
    // General UI texts
    'stats' => 'Statistics',
    'coming_soon' => 'Coming Soon',

    'buttons' => [
        'scroll_to_top' => 'Scroll to top',
        'create' => 'Create',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'save' => 'Save',
        'submit' => 'Submit',
        'back' => 'Back',
        'close' => 'Close',
        'confirm' => 'Confirm',
        'proceed' => 'Proceed',
        'yes' => 'Yes',
        'no' => 'No',
        'view' => 'View',
        'download' => 'Download',
        'upload' => 'Upload',
        'search' => 'Search',
        'reset' => 'Reset',
    ],

    'messages' => [
        'validation_errors' => 'Please fix the errors in the form.',
        'validation_errors_generic' => 'There were errors with your submission. Please check the fields.',
        'data_saved' => 'Data was successfully saved.',
        'data_deleted' => 'Data was successfully deleted.',
        'operation_successful' => 'Operation completed successfully.',
        'operation_failed' => 'An error occurred while performing the operation.',
        'no_data' => 'No data to display.',
        'loading' => 'Loading...',
        'processing' => 'Processing...',
        'confirm_delete' => 'Are you sure you want to delete this item?',
        'action_irreversible' => 'This action cannot be undone.',
    ],

    'labels' => [
        'search' => 'Search',
        'filter' => 'Filter',
        'sort' => 'Sort',
        'from' => 'From',
        'to' => 'To',
        'status' => 'Status',
        'actions' => 'Actions',
        'details' => 'Details',
        'settings' => 'Settings',
    ],

    'placeholders' => [
        'search' => 'Enter search term...',
        'select' => 'Select an item',
    ],

    'statuses' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'completed' => 'Completed',
        'failed' => 'Failed',
    ],

    // Notifications
    'notifications' => [
        'success' => [
            // Collections
            'collection_created' => 'Collection has been successfully created.',
            'collection_updated' => 'Collection has been successfully updated.',
            'collection_deleted' => 'Collection has been successfully deleted.',
            'collection_default_set' => 'Collection has been set as default.',
            'collection_visibility_changed' => 'Collection visibility has been changed.',
            
            // Collection items
            'item_added' => 'Item has been added to collection.',
            'item_updated' => 'Item has been successfully updated.',
            'item_deleted' => 'Item has been successfully deleted.',
            'item_duplicated' => 'Item has been successfully duplicated.',
            'items_duplicated' => 'Items have been successfully duplicated.',
            'items_exported' => 'Export has been successfully started.',
            'items_deleted' => 'Items have been successfully deleted.',
        ],
        'error' => [
            // General errors
            'operation_failed' => 'Operation failed. Please try again.',
            'network_error' => 'Connection error. Please check your internet connection.',
            'server_error' => 'Server error. Please try again later.',
            'validation_error' => 'Please check the entered data.',
            
            // Collections
            'collection_create_failed' => 'Failed to create collection.',
            'collection_update_failed' => 'Failed to update collection.',
            'collection_delete_failed' => 'Failed to delete collection.',
            'collection_default_failed' => 'Failed to set collection as default.',
            'collection_visibility_failed' => 'Failed to change collection visibility.',
            
            // Collection items
            'item_add_failed' => 'Failed to add item to collection.',
            'item_update_failed' => 'Failed to update item.',
            'item_delete_failed' => 'Failed to delete item.',
            'item_duplicate_failed' => 'Failed to duplicate item.',
            'items_duplicate_failed' => 'Failed to duplicate items.',
            'items_export_failed' => 'Failed to export items.',
            'items_delete_failed' => 'Failed to delete items.',
            'items_selection_required' => 'Please select at least one item.',
        ],
        'warning' => [
            'unsaved_changes' => 'You have unsaved changes.',
            'slow_connection' => 'Slow connection. Operation may take longer.',
        ],
        'info' => [
            'auto_save' => 'Changes have been automatically saved.',
            'sync_in_progress' => 'Synchronization in progress...',
        ],
    ],
]; 