<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('links.index')
        : redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/links', [LinkController::class, 'index'])->name('links.index');
    Route::post('/links', [LinkController::class, 'store'])->name('links.store');
    Route::delete('/links/{link}', [LinkController::class, 'destroy'])->name('links.destroy');
});

require __DIR__.'/auth.php';
