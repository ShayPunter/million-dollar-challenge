<?php

use App\Http\Middleware\SecurityHeaders;
use App\Models\User;
use Illuminate\Testing\TestResponse;

// Card 5533: every response carries the security headers, sent by the app
// (Forge's nginx adds only X-Frame-Options and nosniff, and only to 2xx/3xx).

function assertSecurityHeaders(TestResponse $response): void
{
    $response->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    $response->assertHeader('Content-Security-Policy', "base-uri 'self'; object-src 'none'; frame-ancestors 'self'");
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->assertHeader('Permissions-Policy', SecurityHeaders::PERMISSIONS_POLICY);

    // nginx sends X-Frame-Options: SAMEORIGIN; a second value from the app would conflict.
    $response->assertHeaderMissing('X-Frame-Options');
}

test('guest responses carry the security headers', function (string $uri, int $status) {
    $response = $this->get($uri);

    $response->assertStatus($status);
    assertSecurityHeaders($response);
})->with([
    'home page' => ['/', 200],
    'login page' => ['/login', 200],
    'unknown page' => ['/no-such-page', 404],
    'unknown grid' => ['/grid/no-such-grid', 404],
    'dashboard redirect' => ['/dashboard', 302],
    'version page (outside the web group)' => ['/version', 200],
    'health check' => ['/up', 200],
]);

test('a signed-in page carries the security headers', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/profile');

    $response->assertOk();
    assertSecurityHeaders($response);
});
