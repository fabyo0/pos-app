<?php

declare(strict_types=1);

use App\Livewire;
use App\Livewire\Customer\Show;
use App\Livewire\Items\Create;
use App\Livewire\Items\Edit;
use App\Livewire\Items\Index;
use App\Livewire\Management\CreateUser;
use App\Livewire\Management\ListPaymentMethods;
use App\Livewire\Management\ListUsers;
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
        Route::get('/', Index::class)->name('index');
        Route::get('/edit/{record}', Edit::class)->name('edit');
        Route::get('/create', Create::class)->name('create');
    });

    // Inventory
    Route::get('/inventories', Livewire\Inventory\Index::class)->name('inventories');

    // Customers
    Route::prefix('customers')->name('customers.')->group(function (): void {
        Route::get('/', Livewire\Customer\Index::class)->name('index');
        Route::get('/create', Livewire\Customer\Create::class)->name('create');
        Route::get('/edit/{record}', Livewire\Customer\Edit::class)->name('edit');
        Route::get('/{record}', Show::class)->name('show');
    });

    // Sales
    Route::get('/sales', Livewire\Sales\Index::class)->name('sales.index');

    // Pos
    Route::prefix('pos')->name('pos.')->group(function (): void {
        Route::get('/', Livewire\POS\Index::class)->name('index');
    });

    // Management
    Route::prefix('management')->name('management.')->group(function (): void {
        Route::get('/users', ListUsers::class)->name('users');
        Route::get('/user/create', CreateUser::class)->name('user.create');
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
