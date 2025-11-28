<?php

declare(strict_types=1);

use App\Livewire\Customer\ListCustomers;
use App\Livewire\Items\ListInventories;
use App\Livewire\Items\ListItems;
use App\Livewire\Management\ListPaymentMethods;
use App\Livewire\Management\ListUsers;
use App\Livewire\Sales\ListSales;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', fn(): Factory|View => view('welcome'))->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function (): void {

    // Items
    Route::prefix('items')->name('items.')->group(function (): void {
        Route::get('/', ListItems::class)->name('index');
        Route::get('/inventories', ListInventories::class)->name('inventories');
    });

    // Customers
    Route::get('/customers', ListCustomers::class)->name('customers.index');

    // Sales
    Route::get('/sales', ListSales::class)->name('sales.index');

    // Management
    Route::prefix('management')->name('management.')->group(function (): void {
        Route::get('/users', ListUsers::class)->name('users');
        Route::get('/payment-methods', ListPaymentMethods::class)->name('payment-methods');
    });

    // Settings
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
