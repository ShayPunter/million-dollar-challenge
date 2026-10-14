<?php

use App\Http\Controllers\GoogleController;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;

function mockGoogleUser(string $email = 'google-person@example.com', bool $emailVerified = true, string $id = 'google-123'): SocialiteUser
{
    return (new SocialiteUser)
        ->setRaw(['sub' => $id, 'email_verified' => $emailVerified])
        ->setToken('secret-access-token')
        ->setRefreshToken('secret-refresh-token')
        ->map([
            'id' => $id,
            'name' => 'Google Person',
            'email' => $email,
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
        ->and($user->name)->toBe('Google Person')
        ->and($user->email_verified_at)->not->toBeNull();

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

test('a new Google user with an unverified Google email is not marked verified', function () {
    Socialite::shouldReceive('driver->user')->andReturn(mockGoogleUser(emailVerified: false));

    $this->get('/auth/google/callback')->assertRedirect('/dashboard');

    expect(User::where('email', 'google-person@example.com')->firstOrFail()->email_verified_at)->toBeNull();
});

test('Google sign-in links to an existing verified account without touching its password', function () {
    $user = User::factory()->create(['email' => 'google-person@example.com']);
    $passwordHash = $user->password;
    Socialite::shouldReceive('driver->user')->andReturn(mockGoogleUser());

    $this->get('/auth/google/callback')->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);
    $user->refresh();
    expect($user->google_id)->toBe('google-123')
        ->and($user->password)->toBe($passwordHash);
    expect(User::count())->toBe(1);
});

test('Google sign-in refuses to take over an unverified password account', function () {
    $user = User::factory()->unverified()->create(['email' => 'google-person@example.com']);
    $passwordHash = $user->password;
    Socialite::shouldReceive('driver->user')->andReturn(mockGoogleUser());

    $this->get('/auth/google/callback')
        ->assertRedirect(route('login'))
        ->assertSessionHas('error', GoogleController::LINK_REFUSED);

    $this->assertGuest();
    $user->refresh();
    expect($user->google_id)->toBeNull()
        ->and($user->password)->toBe($passwordHash);
});

test('Google sign-in refuses to link when Google has not verified the email', function () {
    $user = User::factory()->create(['email' => 'google-person@example.com']);
    Socialite::shouldReceive('driver->user')->andReturn(mockGoogleUser(emailVerified: false));

    $this->get('/auth/google/callback')->assertRedirect(route('login'));

    $this->assertGuest();
    expect($user->refresh()->google_id)->toBeNull();
});

test('a returning Google user is found by Google id and keeps their password', function () {
    $user = User::factory()->unverified()->create([
        'email' => 'google-person@example.com',
        'google_id' => 'google-123',
    ]);
    $passwordHash = $user->password;
    Socialite::shouldReceive('driver->user')->andReturn(mockGoogleUser());

    $this->get('/auth/google/callback')->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);
    $user->refresh();
    expect($user->password)->toBe($passwordHash)
        ->and($user->email_verified_at)->not->toBeNull();
});
