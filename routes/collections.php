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

Route::resource('/', CollectionController::class)->parameters(['' => 'collection']);

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

// Výsledné routy budou například:
// GET /collections -> collections.index (CollectionController@index)
// GET /collections/create -> collections.create (CollectionController@create)
// POST /collections -> collections.store (CollectionController@store)
// GET /collections/{collection} -> collections.show (CollectionController@show)
// GET /collections/{collection}/edit -> collections.edit (CollectionController@edit)
// PUT/PATCH /collections/{collection} -> collections.update (CollectionController@update)
// DELETE /collections/{collection} -> collections.destroy (CollectionController@destroy)

// Poznámka: Použitím ->parameters(['' => 'collection']) zajistíme,
// že parametr v URL pro resource routy bude pojmenován '{collection}'
// namísto výchozího '{\/}' (což by bylo nevalidní) nebo '{collection}' pokud by resource byl pojmenován.
// Jelikož náš resource je '/', explicitně definujeme jméno parametru.
// Alternativně, pokud bychom resource pojmenovali např. 'items', bylo by to:
// Route::resource('items', CollectionController::class); // a parametr by byl {item}
// Ale vzhledem k tomu, že prefix 'collections' je už v web.php,
// chceme, aby se cesty generovaly jako /collections/{id} a ne /collections/collections/{id} 