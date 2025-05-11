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
        'data_saved' => 'Data was successfully saved.',
        'data_deleted' => 'Data was successfully deleted.',
        'operation_successful' => 'Operation completed successfully.',
        'operation_failed' => 'An error occurred while performing the operation.',
        'no_data' => 'No data to display.',
        'loading' => 'Loading...',
        'processing' => 'Processing...',
        'confirm_delete' => 'Are you sure you want to delete this item?',
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
]; 