<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

test('a password sign-up is sent a verification email', function () {
    Notification::fake();

    $this->post('/register', [
        'name' => 'New Person',
        'email' => 'new-person@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'new-person@example.com')->firstOrFail();
    expect($user->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('an unverified user is sent to the verify-email page', function (string $method, string $uri) {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->{$method}($uri)->assertRedirect(route('verification.notice'));
})->with([
    'dashboard' => ['get', '/dashboard'],
    'projects' => ['get', '/projects'],
    'add a project' => ['post', '/projects'],
    'add revenue' => ['post', '/revenues'],
    'create a grid page' => ['post', '/user-page'],
]);

test('an unverified user can still reach their profile', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->get('/profile')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('mustVerifyEmail', true));
});

test('a verified user reaches the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/dashboard')->assertOk();
});

test('a Google sign-up is verified and never sees the prompt', function () {
    Notification::fake();
    Socialite::shouldReceive('driver->user')->andReturn(
        (new SocialiteUser)
            ->setRaw(['sub' => 'google-456', 'email_verified' => true])
            ->map(['id' => 'google-456', 'name' => 'Google Person', 'email' => 'g@example.com'])
    );

    $this->get('/auth/google/callback')->assertRedirect('/dashboard');
    $this->get('/dashboard')->assertOk();

    Notification::assertNothingSent();
});

test('with verification switched off, an unverified user gets in and no email is sent', function () {
    config(['auth.verification_required' => false]);
    Notification::fake();

    $this->post('/register', [
        'name' => 'New Person',
        'email' => 'new-person@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);
    $this->get('/dashboard')->assertOk();
    $this->get('/profile')->assertInertia(fn ($page) => $page->where('mustVerifyEmail', false));

    Notification::assertNothingSent();
    expect(User::where('email', 'new-person@example.com')->value('email_verified_at'))->toBeNull();
});

test('the backfill verifies existing Google users but not password users or admins', function () {
    config(['app.admin_emails' => ['admin@example.com']]);
    $google = User::factory()->unverified()->create(['google_id' => 'g-1']);
    $password = User::factory()->unverified()->create();
    $admin = User::factory()->unverified()->create(['google_id' => 'g-2', 'email' => 'Admin@example.com']);

    (require database_path('migrations/2026_10_21_000000_backfill_email_verified_at_for_google_users.php'))->up();

    expect($google->fresh()->email_verified_at)->not->toBeNull()
        ->and($password->fresh()->email_verified_at)->toBeNull()
        ->and($admin->fresh()->email_verified_at)->toBeNull()
        ->and(DB::table('users')->whereNotNull('email_verified_at')->count())->toBe(1);
});
