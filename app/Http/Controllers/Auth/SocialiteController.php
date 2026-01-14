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

        try {
            $response = Socialite::driver($provider)->user();

            // Try to find user by provider ID first
            $user = User::where($provider . '_id', $response->getId())->first();

            if ($user) {
                Auth::login($user, remember: true);
                return redirect()->intended(route('dashboard'));
            }

            // Try to find by email
            $user = User::where('email', $response->getEmail())->first();

            if ($user) {
                // Link social account to existing user
                $user->update([
                    $provider . '_id' => $response->getId(),
                ]);

                Auth::login($user, remember: true);
                return redirect()->intended(route('dashboard'));
            }

            // Create new user
            $user = User::create([
                'name' => $this->generateNameFromEmail($response, $provider),  // ✅ Helper method
                'email' => $response->getEmail(),
                'password' => bcrypt(Str::random(32)),
                $provider . '_id' => $response->getId(),
                'email_verified_at' => now(),
            ]);

            event(new Registered($user));

            Auth::login($user, remember: true);

            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', __('Unable to login with :provider. Please try again.', ['provider' => ucfirst($provider)]));
        }
    }

    private function generateNameFromEmail($response, string $provider): string
    {

        if ($name = $response->getName() ?? $response->getNickname()) {
            return $name;
        }

        $email = $response->getEmail();
        $username = explode('@', $email)[0];

        $cleanName = ucfirst(str_replace(['.', '_', '-', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], ' ', $username));

        return trim($cleanName) ?: 'User';
    }

    protected function validateProvider(Request $request): array
    {
        return $this->getValidationFactory()->make(
            $request->route()->parameters(),
            ['provider' => 'in:facebook,google,github'],
        )->validate();
    }
}
