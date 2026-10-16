<?php

use App\Models\Project;
use App\Models\Revenue;
use App\Models\User;

test('guests cannot touch projects', function () {
    $project = Project::factory()->create();

    $this->post('/projects', ['name' => 'Nope'])->assertRedirect('/login');
    $this->put("/projects/{$project->id}", ['name' => 'Nope'])->assertRedirect('/login');
    $this->delete("/projects/{$project->id}")->assertRedirect('/login');
    expect(Project::count())->toBe(1);
});

test('a user can create a project', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from('/dashboard')
        ->post('/projects', ['name' => 'Consulting'])
        ->assertRedirect('/dashboard')
        ->assertSessionHasNoErrors();

    expect($user->projects()->pluck('name')->all())->toBe(['Consulting']);
});

test('a project needs a name of at most 100 characters', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/projects', ['name' => ''])->assertSessionHasErrors('name');
    $this->actingAs($user)->post('/projects', ['name' => str_repeat('a', 101)])->assertSessionHasErrors('name');
    expect(Project::count())->toBe(0);
});

test('a user can rename their project', function () {
    $project = Project::factory()->create(['name' => 'Old']);

    $this->actingAs($project->user)
        ->put("/projects/{$project->id}", ['name' => 'New'])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect($project->fresh()->name)->toBe('New');
});

test('deleting a project deletes its revenue too', function () {
    $project = Project::factory()->create();
    Revenue::factory()->count(2)->for($project)->create(['user_id' => $project->user_id]);
    $other = Revenue::factory()->create();

    $this->actingAs($project->user)
        ->delete("/projects/{$project->id}")
        ->assertRedirect();

    expect(Project::find($project->id))->toBeNull()
        ->and(Revenue::where('project_id', $project->id)->count())->toBe(0)
        ->and(Revenue::find($other->id))->not->toBeNull();
});

test("a user cannot rename or delete another user's project", function () {
    $project = Project::factory()->create(['name' => 'Theirs']);
    Revenue::factory()->for($project)->create(['user_id' => $project->user_id]);
    $intruder = User::factory()->create();

    $this->actingAs($intruder)->put("/projects/{$project->id}", ['name' => 'Mine'])->assertForbidden();
    $this->actingAs($intruder)->delete("/projects/{$project->id}")->assertForbidden();

    expect($project->fresh()->name)->toBe('Theirs')
        ->and($project->revenues()->count())->toBe(1);
});
