<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication
    | for various messages that we need to display to the user.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'password' => 'The provided password is incorrect.',
    'not_verified' => 'Your email address is not verified.',
    
    'login' => [
        'title' => 'Log In',
        'email' => 'Email',
        'password' => 'Password',
        'remember' => 'Remember me',
        'submit' => 'Log in',
        'forgot' => 'Forgot your password?',
        'no_account' => 'Don\'t have an account?',
        'register' => 'Register',
        'or' => 'or',
        'sso' => 'Log in with SSO',
        'google' => 'Log in with Google',
    ],
    
    'register' => [
        'title' => 'Register',
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirm' => 'Confirm Password',
        'submit' => 'Register',
        'has_account' => 'Already have an account?',
        'login' => 'Log in',
    ],
    
    'password_reset' => [
        'title' => 'Reset Password',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirm' => 'Confirm Password',
        'submit' => 'Reset Password',
        'back_to_login' => 'Back to login',
    ],

    'forgot_password' => [
        'title' => 'Forgot Password',
        'description' => 'Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.',
        'email' => 'Email',
        'submit' => 'Send Password Reset Link',
        'back_to_login' => 'Back to login',
    ],
    
    'two_factor' => [
        'title' => 'Two Factor Authentication',
        'subtitle' => 'Please enter the authentication code from your authenticator app to continue',
        'code' => 'Authentication Code',
        'code_placeholder' => 'Enter 6-digit code',
        'submit' => 'Verify',
        'recovery' => 'Use recovery code',
        'invalid_code' => 'Invalid authentication code. Please try again.',
        'no_access' => 'Don\'t have access to your authenticator app?',
        'contact_admin' => 'Contact administrator to restore access to your account.',
    ],
    
    'verify_email' => [
        'title' => 'Verify Email Address',
        'description' => 'Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.',
        'success' => 'A new verification link has been sent to your email address.',
        'submit' => 'Resend Verification Email',
    ],
    
    'logout' => 'Log Out',
    
    // Validation messages
    'validation' => [
        'email' => [
            'required' => 'Email is required',
            'valid' => 'Email must be valid',
        ],
        'password' => [
            'required' => 'Password is required',
            'min_length' => 'Password must be at least :length characters',
            'uppercase' => 'Password must contain at least one uppercase letter',
            'lowercase' => 'Password must contain at least one lowercase letter',
            'number' => 'Password must contain at least one number',
        ],
        'password_confirmation' => [
            'required' => 'Password confirmation is required',
            'match' => 'Passwords do not match',
        ],
        'code' => [
            'required' => 'Authentication code is required',
            'numeric' => 'Authentication code must contain only numbers',
            'length' => 'Authentication code must be :length digits',
        ],
    ],
]; 