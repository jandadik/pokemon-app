<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CollectionItemController;

/*
|--------------------------------------------------------------------------
| Collection Routes
|--------------------------------------------------------------------------
|
| Routy specifické pro správu uživatelských sbírek.
| Tyto routy jsou automaticky prefixovány URL segmentem 'collections'
| a jménem 'collections.' díky grupování v routes/web.php.
|
*/

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('', [CollectionController::class, 'index'])->name('index');
//     Route::post('', [CollectionController::class, 'store'])->name('store');
//     Route::get('/create', [CollectionController::class, 'create'])->name('create');
//     Route::get('/list', [CollectionController::class, 'list'])->name('list');
//     Route::get('/{collection}', [CollectionController::class, 'show'])->name('show');
//     Route::put('/{collection}', [CollectionController::class, 'update'])->name('update');
// });

// Speciální routy MUSÍ být před Route::resource, aby nebyly přepsány
Route::get('simple-list', [CollectionController::class, 'listSimple'])
    ->name('simple-list');

// Další speciální routy můžeme přidat zde

// Tato routa MUSÍ být před Route::resource, aby nebyla přepsána
Route::get('user-list', [CollectionController::class, 'list'])->name('user-list');

// Resource routes pro collections - parametr bude {collection}
Route::resource('', CollectionController::class, [
    'parameters' => ['' => 'collection']
]);

// Routy pro rychlou aktualizaci stavů
Route::patch('/{collection}/toggle-default', [CollectionController::class, 'toggleDefault'])->name('toggle_default');
Route::patch('/{collection}/toggle-visibility', [CollectionController::class, 'toggleVisibility'])->name('toggle_visibility');

// TODO: Routa pro přidání karty do sbírky
// Route::post('/{collection}/cards', [CollectionCardController::class, 'store'])->name('cards.store');

// Hromadné operace s položkami (MUSÍ být před resource routami!)
Route::delete('/{collection}/items/bulk-delete', [CollectionItemController::class, 'bulkDelete'])->name('items.bulk_delete');
Route::post('/{collection}/items/bulk-duplicate', [CollectionItemController::class, 'bulkDuplicate'])->name('items.bulk_duplicate');
Route::patch('/{collection}/items/bulk-edit', [CollectionItemController::class, 'bulkEdit'])->name('items.bulk_edit');
Route::get('/{collection}/items/export', [CollectionItemController::class, 'export'])->name('items.export');

// Routy pro položky sbírky (karty v kolekci)
Route::resource('/{collection}/items', CollectionItemController::class)
    ->parameters(['items' => 'item'])
    ->names('items');

// Demo stránka pro testování integrace úkolů 2.3 + 2.4
Route::get('/demo/card-variant-selection', function () {
    return inertia('Collections/Items/Demo');
})->name('demo.card_variant_selection');

