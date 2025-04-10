<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\SetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Catalog Routes
|--------------------------------------------------------------------------
|
| Routy pro katalog karet
|
*/

// Routy pro sety
Route::prefix('sets')
    ->name('sets.')
    ->group(function () {
        Route::get('/', [SetController::class, 'index'])->name('index');
        Route::get('/{set}', [SetController::class, 'show'])->name('show');
        Route::get('/{set}/cards', [SetController::class, 'cards'])->name('cards');
    });

// Routy pro karty
Route::prefix('cards')
    ->name('cards.')
    ->group(function () {
        Route::get('/', [CardController::class, 'index'])->name('index');
        Route::get('/{card}', [CardController::class, 'show'])->name('show');
    }); 