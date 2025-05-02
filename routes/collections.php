<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CollectionController;

/*
|--------------------------------------------------------------------------
| Collection Routes
|--------------------------------------------------------------------------
|
| Routy specifické pro správu uživatelských sbírek.
|
*/

// Hlavní stránka sbírek
Route::get('/', [CollectionController::class, 'index'])->name('index');

// Zde budou routy pro collections, např.:
// Route::get('/', [CollectionController::class, 'index'])->name('collections.index'); 