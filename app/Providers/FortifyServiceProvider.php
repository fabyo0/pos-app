<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View => view('livewire.auth.login'));
        Fortify::verifyEmailView(fn (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View => view('livewire.auth.verify-email'));
        Fortify::twoFactorChallengeView(fn (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View => view('livewire.auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View => view('livewire.auth.confirm-password'));
        Fortify::registerView(fn (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View => view('livewire.auth.register'));
        Fortify::resetPasswordView(fn (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View => view('livewire.auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View => view('livewire.auth.forgot-password'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', fn(Request $request) => Limit::perMinute(5)->by($request->session()->get('login.id')));

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
