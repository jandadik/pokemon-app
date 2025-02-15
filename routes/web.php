<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\SetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index'])->name('index');

Route::get('/hello', [IndexController::class, 'show'])
    ->name('hello')
    ->middleware('auth');

Route::resource('card', CardController::class)
    ->only(['index', 'show']);

Route::resource('set', SetController::class)
    ->only(['index', 'show']);

Route::get('login', [AuthController::class, 'create'])->name('login');
Route::post('login', [AuthController::class, 'store'])->name('login.store');
Route::delete('logout', [AuthController::class, 'destroy'])->name('logout');

Route::get('user-account/create', [UserAccountController::class, 'create'])->name('user-account.create');
Route::post('user-account', [UserAccountController::class, 'store'])->name('user-account.store');