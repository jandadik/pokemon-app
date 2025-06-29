<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Account Translations
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in the account section
    | for various labels, items and messages.
    |
    */

    // Main Tabs
    'tabs' => [
        'profile' => 'Profile',
        'password' => 'Password',
        'email' => 'Email',
        'notifications' => 'Notifications',
        'security' => 'Security',
        'settings' => 'Settings',
        'delete' => 'Delete Account',
    ],
    
    // General notifications
    'general' => [
        'close' => 'Close',
        'cancel' => 'Cancel',
    ],
    
    'select_section' => 'Select section',
    'unverified' => 'Unverified',
    
    // Profile
    'profile' => [
        'title' => 'Profile',
        'name' => 'Name',
        'phone' => 'Phone',
        'bio' => 'About Me',
        'save' => 'Save Changes',
        'success_message' => 'Profile has been successfully updated',
    ],
    
    // Password
    'password' => [
        'title' => 'Change Password',
        'current' => 'Current Password',
        'new' => 'New Password',
        'confirm' => 'Confirm New Password',
        'change' => 'Change Password',
        'success_message' => 'Password has been successfully changed',
    ],
    
    // Email
    'email' => [
        'title' => 'Email',
        'email' => 'Email',
        'change' => 'Change Email',
        'changed_title' => 'Email Changed',
        'verification_sent' => 'A verification link has been sent to your new email address. Please click the link in that message to complete the email change.',
        'not_verified_title' => 'Email Not Verified',
        'verification_needed' => 'Please verify your email to fully use your account.',
        'resend_verification' => 'Resend Verification',
        'success_message' => 'Email has been successfully changed',
        'verification_resent' => 'Verification email has been resent',
    ],
    
    // Notifications
    'notifications' => [
        'title' => 'Notification Settings',
        'email' => 'Email Notifications',
        'push' => 'Browser Push Notifications',
        'newsletter' => 'Subscribe to Newsletter',
        'success_message' => 'Notification settings have been saved',
        'error_message' => 'Failed to save notification settings',
    ],
    
    // Security
    'security' => [
        'title' => 'Account Security',
        'two_factor' => 'Two-Factor Authentication',
        'login_notifications' => 'New Login Alerts',
        'login_history' => 'Login History',
        'no_login_records' => 'No login records available yet.',
        'suspicious' => 'Suspicious',
        'active_sessions' => 'Active Sessions',
        'logout_other_devices' => 'Logout Other Devices',
        'logout_others_success' => 'Other devices have been successfully logged out',
        'logout_others_error' => 'An error occurred while logging out other devices',
        'login_notifications_saved' => 'Login notification settings have been saved',
        'save_error' => 'Failed to save settings',
        'history_load_error' => 'Failed to load login history',
        'two_factor_enable' => 'Enable Two-Factor Authentication',
        'two_factor_disable' => 'Disable Two-Factor Authentication',
        'two_factor_enable_info' => 'To enable two-factor authentication, scan the following QR code using an authentication app (e.g., Google Authenticator, Authy, or Microsoft Authenticator):',
        'two_factor_disable_warning' => 'Are you sure you want to disable two-factor authentication? This will decrease your account security.',
        'generating_qr' => 'Generating QR code...',
        'qr_code_alt' => 'QR code for two-factor authentication',
        'scan_qr_or_url' => 'Scan the QR code or enter the URL in your authentication app:',
        'copy_url' => 'Copy URL',
        'secret_key' => 'Secret key for manual entry:',
        'copy_key' => 'Copy Key',
        'verification_code' => 'Verification Code',
        'code_required' => 'Enter verification code',
        'code_format' => 'Code must contain 6 digits',
        'remember_device' => 'Remember this device',
        'verify_code' => 'Verify Code',
        'disable' => 'Disable',
    ],
    
    // Settings
    'settings' => [
        'title' => 'Account Settings',
        'language' => 'Interface Language',
        'languages' => [
            'cs' => 'Czech',
            'en' => 'English',
        ],
        'theme' => 'Application Theme',
        'themes' => [
            'light' => 'Light',
            'dark' => 'Dark',
            'system' => 'System Default',
        ],
        'theme_preview' => 'Theme Preview',
        'theme_light' => 'Light Mode',
        'theme_dark' => 'Dark Mode',
        'theme_system' => 'System Default (:mode)',
        'collections' => [
            'title' => 'Collections',
            'auto_save_to_default' => 'Auto-save to default collection',
            'auto_save_to_default_hint' => 'When enabled, cards from catalog will be automatically saved to your default collection. Otherwise, you can select collection manually.',
        ],
        'colors' => [
            'primary' => 'Primary',
            'secondary' => 'Secondary',
            'accent' => 'Accent',
            'error' => 'Error',
        ],
        'save' => 'Save',
        'success_message' => 'Settings have been successfully saved',
        'error_message' => 'An error occurred while saving settings',
    ],
    
    // Delete Account
    'delete' => [
        'title' => 'Delete Account',
        'warning_title' => 'Irreversible Action',
        'warning_text' => 'Deleting your account is an irreversible action. All your data will be permanently removed.',
        'password_confirm' => 'Enter your password to confirm',
        'understand' => 'I understand this action is irreversible',
        'confirm_button' => 'Delete Account',
    ],
]; 