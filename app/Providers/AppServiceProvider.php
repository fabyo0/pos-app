<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->configureCommands();
        $this->configureModels();
        $this->configureUrl();
        $this->configureVite();
        $this->configurePasswordValidation();
        $this->configureCarbonImmutable();
        $this->configureRateLimiting();
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            $this->app->isProduction(),
        );
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict( ! $this->app->isProduction());
        Model::preventLazyLoading( ! $this->app->isProduction());
        Model::unguard();
    }

    private function configureUrl(): void
    {
        URL::forceHttps($this->app->isProduction());
    }

    private function configureVite(): void
    {
        Vite::useAggressivePrefetching();
    }

    private function configurePasswordValidation(): void
    {
        Password::defaults(
            fn() => $this->app->isProduction()
            ? Password::min(8)
                ->uncompromised()
                ->letters()
                ->mixedCase()
                ->numbers()
            : null,
        );
    }

    private function configureCarbonImmutable(): void
    {
        Date::use(CarbonImmutable::class);
        CarbonImmutable::setLocale(config('app.locale'));
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for(
            'global',
            fn(Request $request) => Limit::perMinute(60)->by($request->ip()),
        );

        RateLimiter::for(
            'api',
            fn(Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()),
        );

        RateLimiter::for(
            'auth',
            fn(Request $request) => Limit::perMinute(5)->by($request->ip()),
        );

        RateLimiter::for(
            'login',
            fn(Request $request) => Limit::perMinute(5)
                ->by($request->input('email') . '|' . $request->ip())
                ->response(fn() => response()->json(['message' => 'Too many login attempts.'], 429)),
        );
    }
}
