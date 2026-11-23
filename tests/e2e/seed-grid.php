<?php

// Creates a verified user with a public grid page and the given revenue total
// in the E2E database (see env.js). Usage: php tests/e2e/seed-grid.php my-slug 300000

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Str;

require __DIR__.'/../../vendor/autoload.php';

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

[$slug, $total] = [$argv[1], (float) $argv[2]];

$user = User::create([
    'name' => 'Grid Owner',
    'email' => $slug.'@example.test',
    'password' => Str::random(32),
]);
$user->forceFill(['email_verified_at' => now()])->save();
$user->userPage()->create(['slug' => $slug, 'title' => 'Seeded grid', 'description' => 'Seeded by the E2E suite.']);
$project = $user->projects()->create(['name' => 'Seeded project']);
$user->revenues()->create(['project_id' => $project->id, 'amount' => $total]);

echo $slug;
