<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ClientDomainsController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\DomainRegistrationController;
use App\Http\Controllers\HostingController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');

Route::get('/hosting', [HostingController::class, 'index'])->name('hosting.index');

Route::get('/domain-checker', [DomainController::class, 'index'])->name('domains.index');
Route::post('/check-domains', [DomainController::class, 'search'])->name('domain.check');

Route::post('/cart/update-period', [CartController::class, 'updatePeriod'])->name('cart.update-period');
Route::post('/cart/remove-item', [CartController::class, 'removeItem'])->name('cart.remove-item');
Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('cart.add')->middleware('web');
Route::get('/cart/user/details', [CartController::class, 'cart'])->name('cart.index')->middleware('auth');

// check contacts
Route::get('/check', [TestController::class, 'check'])->name('contact.check');

Route::get('/dashboard', function () {

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/my-domains', [ClientDomainsController::class, 'index'])->name('client.domains');
    Route::get('/domain-registration', [DomainRegistrationController::class, 'create'])->name('contacts.create');
    Route::post('/domains/register', [DomainRegistrationController::class, 'registerDomains'])->name('domains.register');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
