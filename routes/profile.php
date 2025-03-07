<?php

use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SessionController;
use App\Http\Controllers\User\LoginHistoryController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Profile Routes
|--------------------------------------------------------------------------
|
| Routy pro správu uživatelského profilu
| Všechny routy mají middleware 'auth' a '2fa'
|
*/

// Profil management
Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('profile');
    
    // Aktualizace částí profilu
    Route::put('/', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::put('/email', [ProfileController::class, 'updateEmail'])->name('email.update');
    Route::put('/notifications', [ProfileController::class, 'updateNotifications'])->name('notifications.update');
    Route::put('/settings', [ProfileController::class, 'updateSettings'])->name('settings.update');
    Route::put('/security', [ProfileController::class, 'updateSecurity'])->name('security.update');
});

// Další uživatelské routy
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('verification.send');

Route::get('parameters', [ProfileController::class, 'fetchParameters'])->name('parameters.fetch');

// Historie přihlášení
Route::get('/login-history', [LoginHistoryController::class, 'index'])->name('login-history.index');

// Správa sessions
Route::delete('sessions/{session}', [SessionController::class, 'destroy'])->name('sessions.destroy');
