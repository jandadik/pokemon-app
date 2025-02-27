<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\SetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

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