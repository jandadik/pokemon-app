<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RegisterCategoryController;
use App\Http\Controllers\Admin\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routy pro administrační část aplikace
| Všechny routy jsou již pod prefixem 'admin' a mají middleware 'auth' a 'permission:admin.access'
|
*/

// Admin dashboard
Route::get('/', [AdminController::class, 'index'])->name('index');

// Role management
Route::group(['middleware' => 'permission:role.view'], function () {
    Route::resource('roles', RoleController::class);
});

// Permission management
Route::group(['prefix' => 'permissions'], function () {
    Route::get('/', [PermissionController::class, 'index'])
        ->middleware('permission:permission.view')
        ->name('permissions.index');
    
    Route::get('/create', [PermissionController::class, 'create'])
        ->middleware('permission:permission.create')
        ->name('permissions.create');
    
    Route::post('/', [PermissionController::class, 'store'])
        ->middleware('permission:permission.create')
        ->name('permissions.store');
    
    Route::get('/{permission}/edit', [PermissionController::class, 'edit'])
        ->middleware('permission:permission.edit')
        ->name('permissions.edit');
    
    Route::put('/{permission}', [PermissionController::class, 'update'])
        ->middleware('permission:permission.edit')
        ->name('permissions.update');
    
    Route::delete('/{permission}', [PermissionController::class, 'destroy'])
        ->middleware('permission:permission.delete')
        ->name('permissions.destroy');
});

// Register Categories management
Route::group(['prefix' => 'registers'], function () {
    // Register Categories
    Route::get('/', [RegisterCategoryController::class, 'index'])
        ->middleware('permission:register.view')
        ->name('register-categories.index');
    
    Route::post('/', [RegisterCategoryController::class, 'store'])
        ->middleware('permission:register.create')
        ->name('register-categories.store');
    
    Route::put('/{category}', [RegisterCategoryController::class, 'update'])
        ->middleware('permission:register.edit')
        ->name('register-categories.update');
    
    Route::delete('/{category}', [RegisterCategoryController::class, 'destroy'])
        ->middleware('permission:register.delete')
        ->name('register-categories.destroy');
    
    // Register Items
    Route::get('/{category}/items', [RegisterController::class, 'index'])
        ->middleware('permission:register.view')
        ->name('registers.index');
    
    Route::post('/{category}/items', [RegisterController::class, 'store'])
        ->middleware('permission:register.create')
        ->name('registers.store');
    
    Route::put('/{category}/items/{register}', [RegisterController::class, 'update'])
        ->middleware('permission:register.edit')
        ->name('registers.update');
    
    Route::delete('/{category}/items/{register}', [RegisterController::class, 'destroy'])
        ->middleware('permission:register.delete')
        ->name('registers.destroy');
});

// User management
Route::group(['prefix' => 'users', 'middleware' => 'permission:user.view'], function () {
    Route::get('/', [UserController::class, 'index'])
        ->name('users.index');
    
    Route::get('/create', [UserController::class, 'create'])
        ->middleware('permission:user.create')
        ->name('users.create');
    
    Route::post('/', [UserController::class, 'store'])
        ->middleware('permission:user.create')
        ->name('users.store');
    
    Route::get('/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:user.edit')
        ->name('users.edit');
    
    Route::put('/{user}', [UserController::class, 'update'])
        ->middleware('permission:user.edit')
        ->name('users.update');
    
    Route::delete('/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:user.delete')
        ->name('users.destroy');
});
