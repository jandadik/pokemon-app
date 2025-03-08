<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SetController;
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

// Karty a sety - resource routy
Route::resources([
    'card' => CardController::class,
    'set' => SetController::class,
], ['only' => ['index', 'show']]);

// Přepínání jazyků
Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');
