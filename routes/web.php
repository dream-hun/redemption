<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ClientDomainsController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\DomainRegistrationController;
use App\Http\Controllers\HostingController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');

Route::get('/hosting', [HostingController::class, 'index'])->name('hosting.index');

Route::get('/domain-checker', [DomainController::class, 'index'])->name('domains.index');
Route::post('/check-domains', [DomainController::class, 'search'])->name('domain.check');

Route::post('/cart/update-period', [CartController::class, 'updatePeriod'])->name('cart.update-period');
Route::post('/cart/remove-item', [CartController::class, 'removeItem'])->name('cart.remove-item');
Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('cart.add')->middleware('web');
Route::get('/shopping-cart', [CartController::class, 'cart'])->name('cart.index')->middleware('auth');

Route::get('/dashboard', function () {

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Client Domains
    Route::get('/my-domains', [ClientDomainsController::class, 'index'])->name('client.domains');
    Route::get('/my-domains/manage/{domain:name}', [ClientDomainsController::class, 'manage'])->name('client.domains.manage');
    Route::get('/my-domains/{domain}', [ClientDomainsController::class, 'show'])->name('client.domains.show');

    // Domain Management
    Route::get('/my-domains/{domain}/edit-contacts', [DomainRegistrationController::class, 'editContacts'])
        ->name('client.domains.edit-contacts');
    Route::put('/my-domains/{domain}/contacts', [DomainRegistrationController::class, 'updateContacts'])
        ->name('client.domains.update-contacts');

    Route::get('/my-domains/{domain}/edit-nameservers', [DomainRegistrationController::class, 'editNameservers'])
        ->name('client.domains.edit-nameservers');
    Route::put('/my-domains/{domain}/nameservers', [DomainRegistrationController::class, 'updateNameservers'])
        ->name('client.domains.update-nameservers');

    Route::get('/my-domains/{domain}/renew', [DomainRegistrationController::class, 'renewForm'])
        ->name('client.domains.renew');
    Route::put('/my-domains/{domain}/renew', [DomainRegistrationController::class, 'renew']);

    Route::delete('/my-domains/{domain}', [DomainRegistrationController::class, 'destroy'])
        ->name('client.domains.destroy');

    // Domain Registration
    Route::get('/domain-registration', [DomainRegistrationController::class, 'create'])->name('contacts.create');
    Route::post('/domains/register', [DomainRegistrationController::class, 'registerDomains'])->name('domains.register');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
