<?php

use App\Models\Project;
use App\Models\User;

test('the dashboard sends each revenue with only the fields it shows', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $revenue = $user->revenues()->create(['project_id' => $project->id, 'amount' => 1234.5]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard', false)
            ->where('projects.0.amount', fn ($amount) => (float) $amount === 1234.5)
            ->has('projects.0.revenues', 1)
            ->has('projects.0.revenues.0', fn ($row) => $row
                ->where('id', $revenue->id)
                ->where('project_id', $project->id)
                ->where('amount', fn ($amount) => (float) $amount === 1234.5)
                ->has('created_at')
            )
            ->where('stats.completedCubes', fn ($cubes) => (int) $cubes === 1)
        );
});
