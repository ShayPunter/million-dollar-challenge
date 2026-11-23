<?php

// Card 5532: sign-up and reset limits, other sessions end on a password
// change or reset, a new email address needs the current password, and
// "forgot password" doesn't say whether an address has an account.

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

function hardeningSignUp(string $email): array
{
    return [
        'name' => 'Test User',
        'email' => $email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ];
}

test('sign-ups are limited per ip', function () {
    Notification::fake();

    for ($i = 1; $i <= 5; $i++) {
        $this->post('/register', hardeningSignUp("person{$i}@example.com"))->assertSessionHasNoErrors();
        auth()->logout();
    }

    $this->post('/register', hardeningSignUp('person6@example.com'))
        ->assertSessionHasErrors(['email' => 'Too many attempts. Please try again in 60 minutes.']);

    expect(User::query()->where('email', 'like', 'person%')->count())->toBe(5);

    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])
        ->post('/register', hardeningSignUp('person7@example.com'))
        ->assertSessionHasNoErrors();
});

test('password reset requests are limited per ip', function () {
    Notification::fake();

    for ($i = 1; $i <= 6; $i++) {
        $this->post('/forgot-password', ['email' => "nobody{$i}@example.com"])->assertSessionHasNoErrors();
    }

    $this->post('/forgot-password', ['email' => 'nobody7@example.com'])
        ->assertSessionHasErrors(['email' => 'Too many attempts. Please try again in 1 minute.']);
});

test('forgot password answers the same for an unknown address', function () {
    Notification::fake();
    $user = User::factory()->create();

    $responses = [
        $this->post('/forgot-password', ['email' => $user->email]),
        $this->post('/forgot-password', ['email' => 'nobody@example.com']),
        // The broker throttles a repeat for the same account: same answer.
        $this->post('/forgot-password', ['email' => $user->email]),
    ];

    foreach ($responses as $response) {
        $response->assertSessionHasNoErrors()
            ->assertSessionHas('status', __(Password::RESET_LINK_SENT));
    }
});

test('a password reset signs out other sessions', function () {
    $user = User::factory()->create();
    $oldHash = $user->getAuthPassword();
    $token = Password::broker()->createToken($user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'a-new-password-123',
        'password_confirmation' => 'a-new-password-123',
    ])->assertSessionHasNoErrors();

    $this->actingAs($user->fresh())
        ->withSession(['password_hash_web' => $oldHash])
        ->get('/profile')
        ->assertRedirect('/login');

    $this->assertGuest();
});

test('a password change signs out other sessions but not this one', function () {
    $user = User::factory()->create();
    $oldHash = $user->getAuthPassword();

    $this->actingAs($user)->get('/profile')->assertOk();

    $this->from('/profile')->put('/password', [
        'current_password' => 'password',
        'password' => 'a-new-password-123',
        'password_confirmation' => 'a-new-password-123',
    ])->assertSessionHasNoErrors();

    expect(Hash::check('a-new-password-123', $user->fresh()->password))->toBeTrue();

    $this->get('/profile')->assertOk();
    $this->assertAuthenticatedAs($user);

    $this->withSession(['password_hash_web' => $oldHash])
        ->get('/profile')
        ->assertRedirect('/login');
    $this->assertGuest();
});

test('an email change needs the current password', function () {
    $user = User::factory()->create(['email' => 'old@example.com']);

    $this->actingAs($user)
        ->patch('/profile', ['name' => $user->name, 'email' => 'new@example.com'])
        ->assertSessionHasErrors('current_password');

    $this->patch('/profile', [
        'name' => $user->name,
        'email' => 'new@example.com',
        'current_password' => 'wrong-password',
    ])->assertSessionHasErrors('current_password');

    expect($user->fresh()->email)->toBe('old@example.com');

    $this->patch('/profile', [
        'name' => $user->name,
        'email' => 'new@example.com',
        'current_password' => 'password',
    ])->assertSessionHasNoErrors();

    expect($user->fresh()->email)->toBe('new@example.com');
});

test('the home page sends no framework or PHP version', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->missing('laravelVersion')->missing('phpVersion'));
});
