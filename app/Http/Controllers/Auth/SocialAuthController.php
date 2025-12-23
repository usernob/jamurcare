<?php

namespace app\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from provider.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to authenticate with ' . ucfirst($provider));
        }

        // Check if user exists with same email
        $existingUser = User::where('email', $socialUser->getEmail())->first();

        if ($existingUser) {
            // Link social account to existing user
            $existingUser->update([
                'provider_name' => $provider,
                'provider_id' => $socialUser->getId(),
            ]);

            Auth::login($existingUser, true);

            return redirect()->route("dashboard.index", [
                "ulid" => $existingUser->devices()->first()?->ulid
            ]);
        } else {
            // Create new user
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(24)),
                'provider_name' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]);

            Auth::login($user);

            return redirect()->route("device.add.form");
        }
    }
}
