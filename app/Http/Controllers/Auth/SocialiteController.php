<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

final class SocialiteController
{
    use ValidatesRequests;

    public function loginSocial(Request $request, string $provider): RedirectResponse
    {
        $this->validateProvider($request);

        return Socialite::driver($provider)->redirect();
    }

    public function callBackSocial(Request $request, string $provider): RedirectResponse
    {
        $this->validateProvider($request);

        $response = Socialite::driver($provider)->user();

        $user = User::firstOrCreate(
            ['email' => $response->getEmail()],
            [
                'name' => $response->getName() ?? $response->getNickname() ?? explode('@', $response->getEmail())[0],
                'password' => bcrypt(Str::random(32)),
                'email_verified_at' => now(),
            ],
        );

        $data = [$provider . '_id' => $response->getId()];

        if ($user->wasRecentlyCreated) {
            event(new Registered($user));
        }

        $user->update($data);

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }

    protected function validateProvider(Request $request): array
    {
        return $this->getValidationFactory()->make(
            $request->route()->parameters(),
            ['provider' => 'in:facebook,google,github'],
        )->validate();
    }
}
