<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

test('the projects resource only registers the routes it implements', function () {
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'Side hustle']);

    $this->actingAs($user)->get('/projects/create')->assertMethodNotAllowed();
    $this->actingAs($user)->get("/projects/{$project->id}")->assertMethodNotAllowed();
    $this->actingAs($user)->get("/projects/{$project->id}/edit")->assertNotFound();

    $this->actingAs($user)->put("/projects/{$project->id}", ['name' => 'Renamed'])->assertRedirect();
    expect($project->fresh()->name)->toBe('Renamed');
});

test('the dashboard sums project revenue without a query per project', function () {
    $user = User::factory()->create();

    $countQueries = function () use ($user) {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->actingAs($user)->get('/dashboard')->assertOk();
        DB::disableQueryLog();

        return count(DB::getQueryLog());
    };

    $project = $user->projects()->create(['name' => 'One']);
    $user->revenues()->create(['project_id' => $project->id, 'amount' => 1500]);
    $user->revenues()->create(['project_id' => $project->id, 'amount' => 250]);
    $withOne = $countQueries();

    foreach (range(2, 6) as $i) {
        $extra = $user->projects()->create(['name' => "Project {$i}"]);
        $user->revenues()->create(['project_id' => $extra->id, 'amount' => 100]);
    }
    $withSix = $countQueries();

    expect($withSix)->toBe($withOne);

    $this->actingAs($user)->get('/dashboard')
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard', false)
            ->has('projects', 6)
            ->where('projects.0.name', 'One')
            ->where('projects.0.amount', fn ($amount) => (float) $amount === 1750.0)
        );
});

test('revenue amounts above the column limit are rejected, not a server error', function () {
    $user = User::factory()->create();
    $project = $user->projects()->create(['name' => 'Big']);

    $this->actingAs($user)
        ->post('/revenues', ['project_id' => $project->id, 'amount' => 10000000000])
        ->assertSessionHasErrors('amount');

    $revenue = $user->revenues()->create(['project_id' => $project->id, 'amount' => 10]);
    $this->actingAs($user)
        ->put("/revenues/{$revenue->id}", ['amount' => 10000000000])
        ->assertSessionHasErrors('amount');

    $this->actingAs($user)
        ->post('/revenues', ['project_id' => $project->id, 'amount' => 9999999999.99])
        ->assertSessionHasNoErrors();
});

test('the removed v1 challenge API returns 404 instead of a server error', function () {
    $this->get('/v1/challenges/abc')->assertNotFound();
    $this->get('/v1/challenges/abc/progress')->assertNotFound();
    $this->get('/v1/challenges/abc/revenues')->assertNotFound();
    $this->post('/v1/challenges')->assertNotFound();

    $routes = collect(app('router')->getRoutes()->getRoutes())->map->uri();
    expect($routes->filter(fn ($uri) => str_starts_with($uri, 'v1')))->toBeEmpty();
});
