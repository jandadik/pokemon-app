<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Zde jsou zaregistrovány všechny webové routy aplikace.
| Soubor je rozdělen na logické celky, které jsou importovány z externích souborů.
|
*/

// Veřejné routy
Route::prefix('/')
    ->name('public.')
    ->group(base_path('routes/public.php'));

// Autentizační routy
Route::prefix('/')
    ->name('auth.')
    ->group(base_path('routes/auth.php'));

// Admin routy
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'permission:admin.access'])
    ->group(base_path('routes/admin.php'));

// Profil a uživatelské nastavení
Route::prefix('/')
    ->name('user.')
    ->middleware(['auth', '2fa'])
    ->group(base_path('routes/profile.php'));

// Katalog routy
Route::prefix('/')
    ->name('catalog.')
    ->group(base_path('routes/catalog.php'));

// Routy pro sbírky uživatelů
Route::prefix('collections')
    ->name('collections.')
    // ->middleware(['auth', '2fa']) // Dočasně zakomentováno, řešíme v controlleru
    ->group(base_path('routes/collections.php'));

//require __DIR__.'/auth.php';
