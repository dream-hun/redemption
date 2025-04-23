<?php

use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DomainController;
use App\Http\Controllers\Admin\DomainPricingController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RenewDomainController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TransferDomainController;
use App\Http\Controllers\Admin\TransferDomainCController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Api\UserContactController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DomainRegistrationController;
use App\Http\Controllers\HostingController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterDomainController;
use App\Http\Controllers\SearchDomainController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');

Route::get('/hosting', [HostingController::class, 'index'])->name('hosting.index');
Route::get('/admin/domains/transfer/auth-code/{domain}', [TransferDomainCController::class, 'getAuthCode'])->name('authcode');

Route::middleware('auth')->group(function () {
    Route::get('/contacts/{id}/details', [\App\Http\Controllers\Api\ContactController::class, 'details'])->name('contacts.details');
    Route::get('/api/contacts/{id}', [\App\Http\Controllers\Api\ContactController::class, 'details'])->name('api.contacts.details');
});

Route::get('/domains', [SearchDomainController::class, 'index'])->name('domains.index');
Route::post('/check-domains', [SearchDomainController::class, 'search'])->name('domain.check');

Route::get('/shopping-cart', [CartController::class, 'cart'])->name('cart.index');

Route::group(['middleware' => 'auth', 'prefix' => 'admin', 'as' => 'admin.'], function () {
 
    Route::resource('settings', SettingController::class);
    Route::resource('users', UsersController::class);
    Route::resource('roles', RolesController::class);
    Route::resource('permissions', PermissionsController::class);
    Route::resource('domain-pricings', DomainPricingController::class)->except('show');
    Route::resource('hostings', \App\Http\Controllers\Admin\HostingController::class)->except(['show']);

    // Contact management (global)
    Route::group(['prefix' => 'contacts', 'as' => 'contacts.'], function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/create', [ContactController::class, 'create'])->name('create');
        Route::post('/', [ContactController::class, 'store'])->name('store');
        Route::get('/{contact:uuid}/edit', [ContactController::class, 'edit'])->name('edit');
        Route::get('/{contact:uuid}', [ContactController::class, 'show'])->name('show');
        Route::put('/{contact:uuid}', [ContactController::class, 'update'])->name('update');
        Route::delete('/{contact:uuid}', [ContactController::class, 'destroy'])->name('destroy');
    });

    // Domain management routes
    Route::group(['prefix' => 'domains', 'as' => 'domains.'], function () {

        // Core domain actions
        Route::get('/', [DomainController::class, 'index'])->name('index');
        Route::get('/create', [DomainController::class, 'create'])->name('create');
        Route::post('/', [DomainController::class, 'store'])->name('store');
        Route::get('/{domain:uuid}', [DomainController::class, 'show'])->name('show');
        Route::get('/{domain:uuid}/edit', [DomainController::class, 'edit'])->name('edit');
        Route::put('/{domain:uuid}', [DomainController::class, 'update'])->name('update');
        Route::delete('/{domain:uuid}', [DomainController::class, 'destroy'])->name('destroy');


        // Domain operations
        Route::prefix('{domain:uuid}')->group(function () {
            // Contact management
            Route::prefix('contacts')->name('contacts.')->group(function () {
                Route::get('{type}/edit', [ContactController::class, 'edit'])->name('edit');
                Route::put('/', [DomainController::class, 'updateContacts'])->name('update');
                Route::delete('remove/{contactType}', [DomainController::class, 'removeContact'])->name('remove');
            });

            // Nameserver management
            Route::put('nameservers', [DomainController::class, 'updateNameservers'])->name('nameservers.update');
        });

        // Domain renewal
        Route::prefix('renewal')->name('renewal.')->group(function () {
            Route::get('/{uuid}', [RenewDomainController::class, 'index'])->name('index');
            Route::post('/{uuid}', [RenewDomainController::class, 'addToCart'])->name('addToCart');
            Route::put('/{uuid}', [RenewDomainController::class, 'renew'])->name('renew');
        });
        // Domain transfer
        // Transfer routes
        Route::prefix('transfer')->name('transfer.')->group(function () {
            Route::get('/{uuid}', [TransferDomainController::class, 'index'])->name('index');
            Route::put('/{uuid}', [TransferDomainController::class, 'transfer'])->name('transfer');
            Route::post('/auth-code', [TransferDomainController::class, 'getAuthCode'])->name('get-auth-code');
        });

        // Transfers page (Approach 2)
        Route::get('/transfers', [TransferDomainController::class, 'listTransfers'])->name('transfers');

        // Domain registration flow
        Route::prefix('registration')->name('registration.')->group(function () {
            Route::get('/{domain:uuid}', [DomainRegistrationController::class, 'create'])->name('create');
            Route::post('/{domain:uuid}', [DomainRegistrationController::class, 'store'])->name('store');
            Route::get('/{domain:uuid}/success', [DomainRegistrationController::class, 'success'])->name('success');
        });
    });
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');
// Domain registration routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/domain/register', [RegisterDomainController::class, 'index'])->name('domain.register');
    Route::post('/domain/register', [RegisterDomainController::class, 'register'])->name('domain.register');
    Route::post('/domain/contact/create', [RegisterDomainController::class, 'createContact'])->name('domain.contact.create');
    Route::get('/domain/registration/success/{domain}', [RegisterDomainController::class, 'success'])->name('domain.registration.success');
});

Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API routes
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/user/contacts', [UserContactController::class, 'index'])->name('user.contacts');
        Route::get('/contacts/{id}', [UserContactController::class, 'show'])->name('contacts.show');
    });
});

require __DIR__ . '/auth.php';
