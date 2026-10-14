<?php

use App\Models\User;

test('the public grid page does not expose the owner account details', function () {
    $user = User::factory()->create([
        'name' => 'Grid Owner',
        'email' => 'owner-private@example.com',
        'google_id' => 'google-123456789',
    ]);
    $user->userPage()->create([
        'slug' => 'owner-grid',
        'title' => 'Owner Grid',
        'description' => 'A public grid.',
    ]);
    $project = $user->projects()->create(['name' => 'Side hustle']);
    $user->revenues()->create(['amount' => 2500, 'project_id' => $project->id]);

    $response = $this->get('/grid/owner-grid');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('PublicPage', false)
            ->where('stats.user', ['name' => 'Grid Owner'])
            ->where('stats.total_revenue', 2500)
            ->has('stats.projects', 1)
        )
        ->assertDontSee('owner-private@example.com')
        ->assertDontSee('google-123456789')
        ->assertDontSee('email_verified_at');
});
