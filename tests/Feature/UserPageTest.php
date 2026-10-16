<?php

use App\Models\Project;
use App\Models\Revenue;
use App\Models\User;
use App\Models\UserPage;

$page = ['slug' => 'my-grid', 'title' => 'My grid', 'description' => 'On the way to a million.'];

test('a user can create their public page', function () use ($page) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/user-page', $page)
        ->assertRedirect('/dashboard')
        ->assertSessionHasNoErrors();

    expect($user->fresh()->userPage->only(['slug', 'title', 'description']))->toBe($page);
});

test('the page slug must be unique, 3 to 50 characters and alpha-dash', function (string $slug) use ($page) {
    UserPage::factory()->create(['slug' => 'taken']);
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/user-page', [...$page, 'slug' => $slug])
        ->assertSessionHasErrors('slug');

    expect($user->fresh()->userPage)->toBeNull();
})->with([
    'taken' => 'taken',
    'spaces' => 'my grid',
    'slash' => 'my/grid',
    'too short' => 'ab',
    'too long' => str_repeat('a', 51),
    'empty' => '',
]);

test('a user cannot create a second page', function () use ($page) {
    $existing = UserPage::factory()->create(['slug' => 'first']);

    $this->actingAs($existing->user)
        ->post('/user-page', [...$page, 'slug' => 'second'])
        ->assertStatus(422);

    expect(UserPage::count())->toBe(1)
        ->and($existing->fresh()->slug)->toBe('first');
});

test('a user can update their page title and description, but not the slug', function () {
    $existing = UserPage::factory()->create(['slug' => 'keep-me']);

    $this->actingAs($existing->user)
        ->put('/user-page', ['title' => 'New title', 'description' => 'New text', 'slug' => 'changed'])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect($existing->fresh()->only(['slug', 'title', 'description']))
        ->toBe(['slug' => 'keep-me', 'title' => 'New title', 'description' => 'New text']);
});

test('updating without a page is a 404', function () {
    $this->actingAs(User::factory()->create())
        ->put('/user-page', ['title' => 'T', 'description' => 'D'])
        ->assertNotFound();
});

test('an unknown public page is a 404', function () {
    $this->get('/grid/nobody-here')->assertNotFound();
});

test('the public page shows completed squares and progress to the next one', function () {
    $owner = UserPage::factory()->create(['slug' => 'maths'])->user;
    $a = Project::factory()->for($owner)->create(['name' => 'A']);
    $b = Project::factory()->for($owner)->create(['name' => 'B']);
    Revenue::factory()->for($a)->create(['user_id' => $owner->id, 'amount' => 1500]);
    Revenue::factory()->for($a)->create(['user_id' => $owner->id, 'amount' => 999]);
    Revenue::factory()->for($b)->create(['user_id' => $owner->id, 'amount' => 1250]);
    // Someone else's revenue never counts.
    Revenue::factory()->create(['amount' => 100000]);

    $this->get('/grid/maths')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('PublicPage', false)
            ->where('stats.total_revenue', fn ($total) => (float) $total === 3749.0)
            ->where('stats.completed_cubes', fn ($cubes) => (int) $cubes === 3)
            ->where('stats.progress_to_next_cube', fn ($progress) => (int) $progress === 749)
            ->has('stats.projects', 2)
            ->where('stats.projects.0.name', 'A')
            ->where('stats.projects.0.total', fn ($total) => (float) $total === 2499.0)
            ->where('stats.projects.1.total', fn ($total) => (float) $total === 1250.0)
        );
});

test('a page with no revenue shows an empty grid', function () {
    UserPage::factory()->create(['slug' => 'fresh']);

    $this->get('/grid/fresh')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('stats.total_revenue', fn ($total) => (float) $total === 0.0)
            ->where('stats.completed_cubes', fn ($cubes) => (int) $cubes === 0)
            ->where('stats.progress_to_next_cube', fn ($progress) => (int) $progress === 0)
            ->where('stats.last_updated', null)
            ->has('stats.projects', 0)
        );
});

test('exactly a thousand and a million dollars fill whole squares', function (float $amount, int $cubes) {
    $owner = UserPage::factory()->create(['slug' => 'edge'])->user;
    $project = Project::factory()->for($owner)->create();
    Revenue::factory()->for($project)->create(['user_id' => $owner->id, 'amount' => $amount]);

    $this->get('/grid/edge')->assertInertia(fn ($page) => $page
        ->where('stats.completed_cubes', fn ($value) => (int) $value === $cubes)
        ->where('stats.progress_to_next_cube', fn ($value) => (int) $value === 0)
    );
})->with([
    'one square' => [1000, 1],
    'the whole grid' => [1000000, 1000],
]);
