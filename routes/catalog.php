<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\CollectionController;
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
        // Route::get('/lookup', [CardController::class, 'performLookup'])->name('lookup')->middleware(['auth']); // Zakomentováno
        Route::get('/{card}', [CardController::class, 'show'])->name('show');
        
        // Routy pro varianty karet (nezávislé na kolekci)
        Route::get('/{card}/variants', [CardController::class, 'variants'])->name('variants');
        Route::get('/{card}/variants/{variantTypeCode}', [CardController::class, 'variantDetails'])->name('variants.details');
    });

// Routa pro card lookup (vyhledávání karet pro přidání do sbírky apod.)
// Používá se např. v modálním okně pro rychlé vyhledání karty
Route::middleware('auth')->group(function () {
    Route::get('/lookup', [CardController::class, 'performLookup'])->name('cards.lookup');
    
    // Routy pro správu uživatelských sbírek
    Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::post('/collections', [CollectionController::class, 'store'])->name('collections.store');
    Route::get('/collections/create', [CollectionController::class, 'create'])->name('collections.create');
    Route::get('/collections/{collection}', [CollectionController::class, 'show'])->name('collections.show');
    Route::put('/collections/{collection}', [CollectionController::class, 'update'])->name('collections.update');
    Route::delete('/collections/{collection}', [CollectionController::class, 'destroy'])->name('collections.destroy');
    Route::get('/collections/{collection}/edit', [CollectionController::class, 'edit'])->name('collections.edit');
}); 