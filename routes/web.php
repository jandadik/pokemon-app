<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CardPricesController;
use App\Http\Controllers\AttackController;
use App\Http\Controllers\TcgPriceController;
use App\Http\Controllers\CardmarketPriceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/sets', [SetController::class, 'index'])->name('sets.index');
    Route::get('/sets/{set}/cards', [CardController::class, 'index'])->name('sets.cards');
    Route::get('/sets/{set}/cards/{card}', [CardController::class, 'show'])->name('sets.cards.show');
    Route::get('/api/cards/{card}/prices', [CardPricesController::class, 'show'])
        ->name('api.cards.prices')
        ->middleware(['web']);
    Route::get('/api/cards/{card}/attacks', [AttackController::class, 'index'])
        ->name('api.cards.attacks');
    Route::get('/api/cards/{card}/prices/tcg', [TcgPriceController::class, 'show'])
        ->name('api.cards.prices.tcg');
    Route::get('/api/cards/{card}/prices/cardmarket', [CardmarketPriceController::class, 'show'])
        ->name('api.cards.prices.cardmarket');
});

require __DIR__.'/auth.php';
