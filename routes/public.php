<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| Veřejné routy dostupné bez přihlášení
|
*/

// Hlavní stránka
Route::get('/', [IndexController::class, 'index'])
    ->name('index');

// Hello route s admin rolí
Route::get('/hello', [IndexController::class, 'show'])
    ->name('hello')
    ->middleware('auth', 'role:admin');

// Přepínání jazyků
Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');
