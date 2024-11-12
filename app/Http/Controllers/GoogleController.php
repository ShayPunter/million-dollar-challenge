<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

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

            // Debug log
            Log::info('Google user:', ['user' => $googleUser]);

            $user = User::updateOrCreate(
                [
                    'email' => $googleUser->email,  // Changed this to use email as unique identifier
                ],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'password' => encrypt(random_bytes(16)),
                ]
            );

            // Debug log
            Log::info('Created/Updated user:', ['user' => $user]);

            Auth::login($user);

            // Debug log
            Log::info('Auth check:', ['authenticated' => Auth::check()]);

            return redirect()->intended('/dashboard');  // Changed to intended()

        } catch (\Exception $e) {
            // Log the error
            Log::error('Google auth error:', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }
}
