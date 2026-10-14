<?php

use App\Models\User;

test('guests are redirected to login', function () {
    $this->get('/admin')->assertRedirect('/login');
});

test('non-admin users cannot access the admin panel', function () {
    $user = User::factory()->create(['email' => 'someone@example.com']);

    $this->actingAs($user)->get('/admin')->assertForbidden();
});

test('the admin can see all users', function () {
    $admin = User::factory()->create(['email' => 'shay@shaypunter.co.uk', 'google_id' => 'g-admin']);
    User::factory()->count(2)->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Users', false)
            ->has('users', 3)
            ->where('stats.totalUsers', 3)
        );
});

test('admin email matching is case-insensitive', function () {
    $admin = User::factory()->create(['email' => 'Shay@ShayPunter.co.uk', 'google_id' => 'g-admin']);

    $this->actingAs($admin)->get('/admin')->assertOk();
});

test('a password account with the admin email cannot access the admin panel', function () {
    $user = User::factory()->create(['email' => 'shay@shaypunter.co.uk', 'google_id' => null]);

    $this->actingAs($user)->get('/admin')->assertForbidden();
});

test('an unverified admin email cannot access the admin panel', function () {
    $user = User::factory()->unverified()->create(['email' => 'shay@shaypunter.co.uk', 'google_id' => 'g-admin']);

    $this->actingAs($user)->get('/admin')->assertForbidden();
});
