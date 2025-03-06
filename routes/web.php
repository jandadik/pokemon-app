<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\SetController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserAccountController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RegisterCategoryController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\TwoFactorAuthenticationController;
use App\Http\Controllers\User\SessionController;
use App\Http\Controllers\User\LoginHistoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', [IndexController::class, 'index'])->name('index');

Route::get('/hello', [IndexController::class, 'show'])
    ->name('hello')
    ->middleware('auth', 'role:admin');

Route::resource('card', CardController::class)
    ->only(['index', 'show']);

Route::resource('set', SetController::class)
    ->only(['index', 'show']);

Route::get('login', [AuthController::class, 'create'])->name('login');
Route::post('login', [AuthController::class, 'store'])->name('login.store');
Route::delete('logout', [AuthController::class, 'destroy'])->name('logout');

Route::get('user-account/create', [UserAccountController::class, 'create'])->name('user-account.create');
Route::post('user-account', [UserAccountController::class, 'store'])->name('user-account.store');

Route::middleware(['auth', 'permission:admin.access'])->prefix('admin')->name('admin.')->group(function () {
    // Admin dashboard
    Route::get('/', [AdminController::class, 'index'])->name('index');
    
    // Role management
    Route::get('roles', [RoleController::class, 'index'])
        ->middleware('permission:role.view')
        ->name('roles.index');
        
    Route::get('roles/create', [RoleController::class, 'create'])
        ->name('roles.create');
        
    Route::post('roles', [RoleController::class, 'store'])
        ->name('roles.store');
        
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])
        ->name('roles.edit');
        
    Route::put('roles/{role}', [RoleController::class, 'update'])
        ->name('roles.update');
        
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])
        ->name('roles.destroy');

    // Permission management
    Route::get('permissions', [PermissionController::class, 'index'])
        ->middleware('permission:permission.view')
        ->name('permissions.index');
        
    Route::get('permissions/create', [PermissionController::class, 'create'])
        ->middleware('permission:permission.create')
        ->name('permissions.create');
        
    Route::post('permissions', [PermissionController::class, 'store'])
        ->middleware('permission:permission.create')
        ->name('permissions.store');
        
    Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])
        ->middleware('permission:permission.edit')
        ->name('permissions.edit');
        
    Route::put('permissions/{permission}', [PermissionController::class, 'update'])
        ->middleware('permission:permission.edit')
        ->name('permissions.update');
        
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])
        ->middleware('permission:permission.delete')
        ->name('permissions.destroy');

    // Register Categories management
    Route::get('registers', [RegisterCategoryController::class, 'index'])
        ->middleware('permission:register.view')
        ->name('register-categories.index');
        
    Route::post('registers', [RegisterCategoryController::class, 'store'])
        ->middleware('permission:register.create')
        ->name('register-categories.store');
        
    Route::put('registers/{category}', [RegisterCategoryController::class, 'update'])
        ->middleware('permission:register.edit')
        ->name('register-categories.update');
        
    Route::delete('registers/{category}', [RegisterCategoryController::class, 'destroy'])
        ->middleware('permission:register.delete')
        ->name('register-categories.destroy');

    // Register Items management
    Route::get('registers/{category}/items', [RegisterController::class, 'index'])
        ->middleware('permission:register.view')
        ->name('registers.index');
        
    Route::post('registers/{category}/items', [RegisterController::class, 'store'])
        ->middleware('permission:register.create')
        ->name('registers.store');
        
    Route::put('registers/{category}/items/{register}', [RegisterController::class, 'update'])
        ->middleware('permission:register.edit')
        ->name('registers.update');
        
    Route::delete('registers/{category}/items/{register}', [RegisterController::class, 'destroy'])
        ->middleware('permission:register.delete')
        ->name('registers.destroy');

    // User management
    Route::get('users', [UserController::class, 'index'])
        ->middleware('permission:user.view')
        ->name('users.index');
        
    Route::get('users/create', [UserController::class, 'create'])
        ->middleware('permission:user.create')
        ->name('users.create');
        
    Route::post('users', [UserController::class, 'store'])
        ->middleware('permission:user.create')
        ->name('users.store');
        
    Route::get('users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:user.edit')
        ->name('users.edit');
        
    Route::put('users/{user}', [UserController::class, 'update'])
        ->middleware('permission:user.edit')
        ->name('users.update');
        
    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:user.delete')
        ->name('users.destroy');
});

// Routy pro správu profilu
Route::middleware(['auth', '2fa'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::put('/profile/email', [ProfileController::class, 'updateEmail'])->name('email.update');
    Route::put('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('notifications.update');
    Route::put('/profile/settings', [ProfileController::class, 'updateSettings'])->name('settings.update');
    Route::put('/profile/security', [ProfileController::class, 'updateSecurity'])->name('security.update');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('parameters', [ProfileController::class, 'fetchParameters'])->name('parameters.fetch');
    
    // Historie přihlášení
    Route::get('/login-history', [LoginHistoryController::class, 'index'])->name('login-history.index');
    
    // Testovací route pro ověření dat (jen dočasně)
    Route::get('/test-login-history', function() {
        $user = Auth::user();
        $history = \App\Models\LoginHistory::where('user_id', $user->id)->get();
        
        return response()->json([
            'user_id' => $user->id,
            'count' => $history->count(),
            'history' => $history
        ]);
    })->name('test-login-history');
});

// Routy pro reset hesla
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

// Routy pro verifikaci emailu
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

// Routy pro dvoufaktorové ověření
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

// Routy pro správu sessions
Route::middleware(['auth'])->group(function () {
    Route::delete('sessions/{session}', [SessionController::class, 'destroy'])
        ->name('sessions.destroy');
});

// WorkOS SSO routes
Route::get('/auth/workos', [App\Http\Controllers\Auth\WorkOSController::class, 'redirect'])
    ->name('auth.workos');
    
Route::get('/authenticate', [App\Http\Controllers\Auth\WorkOSController::class, 'callback'])
    ->name('auth.workos.callback');