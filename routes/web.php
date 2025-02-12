<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\EppController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EppController::class, 'connect'])->name('epp.connect');

Route::get('/domain-checker', [DomainController::class, 'index'])->name('domain.checker');
Route::post('/check-domains', [DomainController::class, 'search'])->name('domain.check');

Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('cart.add')->middleware('web');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
