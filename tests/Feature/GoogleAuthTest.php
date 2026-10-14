<?php

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;

function mockGoogleUser(): SocialiteUser
{
    return (new SocialiteUser)
        ->setRaw(['sub' => 'google-123'])
        ->setToken('secret-access-token')
        ->setRefreshToken('secret-refresh-token')
        ->map([
            'id' => 'google-123',
            'name' => 'Google Person',
            'email' => 'google-person@example.com',
        ]);
}

test('a successful Google callback signs the user in without logging tokens', function () {
    Log::spy();
    Socialite::shouldReceive('driver->user')->andReturn(mockGoogleUser());

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();

    $user = User::where('email', 'google-person@example.com')->firstOrFail();
    expect($user->google_id)->toBe('google-123')
        ->and($user->name)->toBe('Google Person');

    Log::shouldNotHaveReceived('info');
    Log::shouldNotHaveReceived('warning');
    Log::shouldNotHaveReceived('error');
});

test('a failed Google callback shows a generic error and logs no details from Google', function () {
    Log::spy();
    Socialite::shouldReceive('driver->user')->andThrow(new InvalidStateException('state mismatch'));

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('login'))
        ->assertSessionHas('error', 'Google sign-in failed, please try again.');
    $this->assertGuest();

    Log::shouldHaveReceived('warning')
        ->once()
        ->withArgs(fn ($message, $context) => $message === 'Google sign-in failed'
            && $context['exception'] === InvalidStateException::class
            && ! str_contains(json_encode($context), 'token'));
});

test('the login page shows the Google sign-in error', function () {
    $this->withSession(['error' => 'Google sign-in failed, please try again.'])
        ->get('/login')
        ->assertInertia(fn ($page) => $page
            ->component('Auth/Login', false)
            ->where('error', 'Google sign-in failed, please try again.')
        );
});
