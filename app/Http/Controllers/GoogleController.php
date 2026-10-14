<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as GoogleUser;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    public const LINK_REFUSED = 'An account with this email already exists. Log in with your password instead.';

    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = $this->findOrCreateUser($googleUser);
        } catch (Throwable $e) {
            // Log only the exception type and message: never the Socialite user or its tokens.
            Log::warning('Google sign-in failed', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('login')->with('error', 'Google sign-in failed, please try again.');
        }

        if ($user === null) {
            return redirect()->route('login')->with('error', self::LINK_REFUSED);
        }

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }

    /**
     * Returns null when the email belongs to an account we must not link: a password
     * account whose email was never verified could have been registered by someone else.
     */
    private function findOrCreateUser(GoogleUser $googleUser): ?User
    {
        $googleVerified = ($googleUser->getRaw()['email_verified'] ?? false) === true;

        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user !== null) {
            if ($googleVerified && $user->email_verified_at === null
                && strcasecmp($user->email, $googleUser->getEmail()) === 0) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            return $user;
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user !== null) {
            if (! $googleVerified || $user->email_verified_at === null) {
                return null;
            }

            // Link the Google account; the existing password is left alone.
            $user->forceFill(['google_id' => $googleUser->getId()])->save();

            return $user;
        }

        $user = User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'password' => Str::random(40),
        ]);

        if ($googleVerified) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return $user;
    }
}
