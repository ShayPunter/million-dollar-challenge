<?php

// Prints the signed link from a user's verification email (see env.js).
// Usage: php tests/e2e/verification-url.php person@example.test

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\URL;

require __DIR__.'/../../vendor/autoload.php';

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$user = User::where('email', $argv[1])->firstOrFail();

echo URL::temporarySignedRoute('verification.verify', now()->addHour(), [
    'id' => $user->id,
    'hash' => sha1($user->getEmailForVerification()),
]);
