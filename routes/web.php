<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\SocialiteController;
use App\Livewire;
use App\Livewire\BackupManager;
use App\Livewire\Customer\Show;
use App\Livewire\Items\Create;
use App\Livewire\Items\Edit;
use App\Livewire\Items\Index;
use App\Livewire\Management\AuthenticationLogs;
use App\Livewire\Management\CreateRole;
use App\Livewire\Management\CreateUser;
use App\Livewire\Management\EditRole;
use App\Livewire\Management\ListPaymentMethods;
use App\Livewire\Management\ListUsers;
use App\Livewire\Management\Roles;
use App\Livewire\NotificationsList;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', fn (): Factory|View => view('welcome'))->name('home');

Route::get('dashboard', Livewire\Dashboard\Index::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/test',function (){

    $user = \App\Models\User::find(1);
    $user->notify(new \App\Notifications\SystemWarning('Test bildirim'));
   $count =  \DB::table('notifications')->count();

   dd($count);
});


// Social Auth
Route::get('auth/{provider}/redirect', [SocialiteController::class, 'loginSocial'])
    ->name('socialite.auth');

Route::get('auth/{provider}/callback', [SocialiteController::class, 'callbackSocial'])
    ->name('socialite.callback');

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
        Route::get('/', Livewire\Pos\Index::class)->name('index');
    });

    // Management
    Route::prefix('management')->name('management.')->group(function (): void {
        Route::get('/users', ListUsers::class)->name('users');
        Route::get('/user/create', CreateUser::class)->name('user.create');
        Route::get('/payment-methods', ListPaymentMethods::class)->name('payment-methods');
    });

    // Backup
    Route::get('/backups', BackupManager::class)->name('backups.index');

    // Settings
    Route::redirect('settings', 'settings/profile');

    // Roles Management
    Route::get('/management/roles', Roles::class)
        ->name('management.roles')
        ->middleware('can:roles.view');

    Route::get('/management/roles/create', CreateRole::class)
        ->name('management.roles.create')
        ->middleware('can:roles.create');

    Route::get('/management/roles/{role}/edit', EditRole::class)
        ->name('management.roles.edit')
        ->middleware('can:roles.edit');

    // Authentication Logs
    Route::get('/management/authentication-logs', AuthenticationLogs::class)
        ->name('management.authentication-logs')
        ->middleware('can:authentication-logs.view');


    // Notifications
    Route::get('/notifications', NotificationsList::class)->name('notifications.index');

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
