<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Todo routes
Route::prefix('todos')->name('todos.')->group(function () {
    Route::get('/', [App\Http\Controllers\TodoItemController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\TodoItemController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\TodoItemController::class, 'store'])->name('store');
    Route::get('/{todo}/edit', [App\Http\Controllers\TodoItemController::class, 'edit'])->name('edit');
    Route::put('/{todo}', [App\Http\Controllers\TodoItemController::class, 'update'])->name('update');
    Route::delete('/{todo}', [App\Http\Controllers\TodoItemController::class, 'destroy'])->name('destroy');
});

// Shopping list routes
Route::prefix('shopping')->name('shopping.')->group(function () {
    Route::get('/', [App\Http\Controllers\ShoppingListItemController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ShoppingListItemController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\ShoppingListItemController::class, 'store'])->name('store');
    Route::get('/{shopping}/edit', [App\Http\Controllers\ShoppingListItemController::class, 'edit'])->name('edit');
    Route::put('/{shopping}', [App\Http\Controllers\ShoppingListItemController::class, 'update'])->name('update');
    Route::delete('/{shopping}', [App\Http\Controllers\ShoppingListItemController::class, 'destroy'])->name('destroy');
    Route::post('/checkout', [App\Http\Controllers\ShoppingListItemController::class, 'checkout'])->middleware('auth')->name('checkout');
    
    // Shopping history routes (requires auth)
    Route::prefix('history')->name('history.')->middleware('auth')->group(function () {
        Route::get('/', [App\Http\Controllers\ShoppingHistoryController::class, 'index'])->name('index');
        Route::post('/add', [App\Http\Controllers\ShoppingHistoryController::class, 'store'])->name('add');
    });
});

// Sync routes (requires auth)
Route::prefix('sync')->name('sync.')->middleware('auth')->group(function () {
    Route::post('/', [App\Http\Controllers\SyncController::class, 'sync'])->name('sync');
});

require __DIR__.'/settings.php';
