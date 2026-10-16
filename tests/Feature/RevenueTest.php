<?php

use App\Models\Project;
use App\Models\Revenue;
use App\Models\User;

test('a user can add revenue to their project', function () {
    $project = Project::factory()->create();

    $this->actingAs($project->user)
        ->post('/revenues', ['project_id' => $project->id, 'amount' => 1234.56])
        ->assertRedirect('/dashboard')
        ->assertSessionHasNoErrors();

    $revenue = Revenue::sole();
    expect($revenue->user_id)->toBe($project->user_id)
        ->and($revenue->project_id)->toBe($project->id)
        ->and((float) $revenue->amount)->toBe(1234.56);
});

test('revenue needs an existing project and a non-negative amount', function () {
    $project = Project::factory()->create();

    $this->actingAs($project->user)
        ->post('/revenues', ['project_id' => 999999, 'amount' => 10])
        ->assertSessionHasErrors('project_id');
    $this->actingAs($project->user)
        ->post('/revenues', ['project_id' => $project->id, 'amount' => -1])
        ->assertSessionHasErrors('amount');
    $this->actingAs($project->user)
        ->post('/revenues', ['project_id' => $project->id, 'amount' => 'lots'])
        ->assertSessionHasErrors('amount');

    expect(Revenue::count())->toBe(0);
});

test("a user cannot add revenue to another user's project", function () {
    $project = Project::factory()->create();
    $intruder = User::factory()->create();

    $this->actingAs($intruder)
        ->post('/revenues', ['project_id' => $project->id, 'amount' => 500])
        ->assertForbidden();

    expect(Revenue::count())->toBe(0);
});

test('a user can edit and delete their revenue', function () {
    $revenue = Revenue::factory()->create(['amount' => 100]);
    $owner = $revenue->user;

    $this->actingAs($owner)
        ->put("/revenues/{$revenue->id}", ['amount' => 250.5])
        ->assertRedirect()
        ->assertSessionHasNoErrors();
    expect((float) $revenue->fresh()->amount)->toBe(250.5);

    $this->actingAs($owner)->delete("/revenues/{$revenue->id}")->assertRedirect();
    expect(Revenue::find($revenue->id))->toBeNull();
});

test("a user cannot edit or delete another user's revenue", function () {
    $revenue = Revenue::factory()->create(['amount' => 100]);
    $intruder = User::factory()->create();

    $this->actingAs($intruder)->put("/revenues/{$revenue->id}", ['amount' => 1])->assertForbidden();
    $this->actingAs($intruder)->delete("/revenues/{$revenue->id}")->assertForbidden();

    expect((float) $revenue->fresh()->amount)->toBe(100.0);
});

test('guests cannot touch revenue', function () {
    $revenue = Revenue::factory()->create();

    $this->post('/revenues', ['project_id' => $revenue->project_id, 'amount' => 1])->assertRedirect('/login');
    $this->put("/revenues/{$revenue->id}", ['amount' => 1])->assertRedirect('/login');
    $this->delete("/revenues/{$revenue->id}")->assertRedirect('/login');
    expect(Revenue::count())->toBe(1);
});
