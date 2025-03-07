<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserAccountController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\TwoFactorAuthenticationController;
use App\Http\Controllers\Auth\WorkOSController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Routy pro přihlášení, registraci, ověření emailu a 2FA
|
*/

// Přihlášení a odhlášení
Route::get('login', [AuthController::class, 'create'])->name('login');
Route::post('login', [AuthController::class, 'store'])->name('login.store');
Route::delete('logout', [AuthController::class, 'destroy'])->name('logout');

// Registrace nového účtu
Route::get('user-account/create', [UserAccountController::class, 'create'])->name('user-account.create');
Route::post('user-account', [UserAccountController::class, 'store'])->name('user-account.store');

// Reset hesla
Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Verifikace emailu
Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Dvoufaktorové ověření
Route::middleware(['auth'])->group(function () {
    Route::get('two-factor/qr-code', [TwoFactorAuthenticationController::class, 'generateQrCode'])
        ->name('two-factor.qr-code');
    Route::post('two-factor/enable', [TwoFactorAuthenticationController::class, 'enable'])
        ->name('two-factor.enable');
    Route::delete('two-factor/disable', [TwoFactorAuthenticationController::class, 'disable'])
        ->name('two-factor.disable');
    Route::post('two-factor/verify', [TwoFactorAuthenticationController::class, 'verify'])
        ->name('two-factor.verify');
    Route::get('two-factor/challenge', [TwoFactorAuthenticationController::class, 'challenge'])
        ->name('two-factor.challenge');
});

// WorkOS SSO autentizace
Route::get('/auth/workos', [WorkOSController::class, 'redirect'])
    ->name('workos');
Route::get('/authenticate', [WorkOSController::class, 'callback'])
    ->name('workos.callback');
