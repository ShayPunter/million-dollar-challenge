<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate(
                [
                    'email' => $googleUser->email,
                ],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'password' => encrypt(random_bytes(16)),
                ]
            );

            Auth::login($user);

            return redirect()->intended('/dashboard');
        } catch (Throwable $e) {
            // Log only the exception type and message: never the Socialite user or its tokens.
            Log::warning('Google sign-in failed', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('login')->with('error', 'Google sign-in failed, please try again.');
        }
    }
}
