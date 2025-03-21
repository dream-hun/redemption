<?php

use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DomainController;
use App\Http\Controllers\Admin\DomainPricingController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ClientDomainsController;
use App\Http\Controllers\DomainRegistrationController;
use App\Http\Controllers\HostingController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchDomainController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');

Route::get('/hosting', [HostingController::class, 'index'])->name('hosting.index');

Route::get('/domains', [SearchDomainController::class, 'index'])->name('domains.index');
Route::post('/check-domains', [SearchDomainController::class, 'search'])->name('domain.check');

Route::get('/shopping-cart', [CartController::class, 'cart'])->name('cart.index');

Route::group(['middleware' => 'auth', 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::resource('settings', SettingController::class);
    Route::resource('users', UsersController::class);
    Route::resource('roles', RolesController::class);
    Route::resource('permissions', PermissionsController::class);

    // Contact management (global)
    Route::group(['prefix' => 'contacts', 'as' => 'contacts.'], function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/create', [ContactController::class, 'create'])->name('create');
        Route::post('/', [ContactController::class, 'store'])->name('store');
    });

    Route::resource('domain-pricings', DomainPricingController::class)->except('show');

    // Domain management routes
    Route::group(['prefix' => 'domains', 'as' => 'domains.'], function () {

        // Core domain actions
        Route::get('/', [DomainController::class, 'index'])->name('index');
        Route::get('/create', [DomainController::class, 'create'])->name('create');
        Route::post('/', [DomainController::class, 'store'])->name('store');
        Route::get('/{domain:uuid}', [DomainController::class, 'show'])->name('show');
        Route::get('/{domain:uuid}/edit', [DomainController::class, 'edit'])->name('edit');
        Route::delete('/{domain:uuid}', [DomainController::class, 'destroy'])->name('destroy');

        // Domain operations
        Route::prefix('{domain:uuid}')->group(function () {
            // Contact management
            Route::prefix('contacts')->name('contacts.')->group(function () {
                Route::get('{type}/edit', [ContactController::class, 'edit'])->name('edit');
                Route::put('{type}', [DomainRegistrationController::class, 'updateContacts'])->name('update');
            });
            
            // Nameserver management
            Route::put('nameservers', [DomainRegistrationController::class, 'updateNameservers'])->name('nameservers.update');
            
            // Domain renewal
            Route::put('renew', [DomainRegistrationController::class, 'renew'])->name('renew');
        });

        // Domain registration flow
        Route::prefix('registration')->name('registration.')->group(function () {
            Route::get('/{domain:uuid}', [DomainRegistrationController::class, 'create'])->name('create');
            Route::post('/{domain:uuid}', [DomainRegistrationController::class, 'store'])->name('store');
            Route::get('/{domain:uuid}/success', [DomainRegistrationController::class, 'success'])->name('success');
        });
    });
});


Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Client Domains
    Route::get('/my-domains', [ClientDomainsController::class, 'index'])->name('client.domains');
    Route::get('/my-domains/manage/{domain:name}', [ClientDomainsController::class, 'manage'])->name('client.domains.manage');
    Route::get('/my-domains/{domain}', [ClientDomainsController::class, 'show'])->name('client.domains.show');

    // Domain Management
    Route::get('/my-domains/{domain}/edit-contacts', [DomainRegistrationController::class, 'editContacts'])
        ->name('client.domains.edit-contacts');

    Route::get('/my-domains/{domain}/renew', [DomainRegistrationController::class, 'renewForm'])
        ->name('client.domains.renew');
    Route::put('/my-domains/{domain}/renew', [DomainRegistrationController::class, 'renew']);

    Route::delete('/my-domains/{domain}', [DomainRegistrationController::class, 'destroy'])
        ->name('client.domains.destroy');

    // Domain Registration
    Route::get('/domain-registration/{domain}', [DomainRegistrationController::class, 'create'])->name('contacts.create');
    Route::post('/domains/register', [DomainRegistrationController::class, 'registerDomains'])->name('domains.register');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
