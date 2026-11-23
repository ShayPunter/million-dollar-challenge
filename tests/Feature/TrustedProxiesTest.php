<?php

// Card 5703: the site sits behind Cloudflare. Only Cloudflare's published
// ranges are trusted for X-Forwarded-For/-Proto, so $request->ip() is the
// visitor, and a forged header from anywhere else changes nothing.

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

const CLOUDFLARE_EDGE_V4 = '162.158.10.20';
const CLOUDFLARE_EDGE_V6 = '2606:4700::1';
const VISITOR_IP = '203.0.113.5';

beforeEach(function () {
    // TrustProxies is global middleware, so a route defined here goes through it.
    Route::get('/_test/client', fn (Request $request) => [
        'ip' => $request->ip(),
        'secure' => $request->isSecure(),
        'url' => $request->url(),
    ]);
});

test('a cloudflare request gets the visitor address', function () {
    $this->withServerVariables(['REMOTE_ADDR' => CLOUDFLARE_EDGE_V4])
        ->withHeaders(['X-Forwarded-For' => VISITOR_IP, 'X-Forwarded-Proto' => 'https'])
        ->getJson('/_test/client')
        ->assertJson(['ip' => VISITOR_IP, 'secure' => true]);
});

test('an address the visitor put in front of cloudflare\'s is skipped', function () {
    $this->withServerVariables(['REMOTE_ADDR' => CLOUDFLARE_EDGE_V6])
        ->withHeaders(['X-Forwarded-For' => '10.0.0.1, '.VISITOR_IP])
        ->getJson('/_test/client')
        ->assertJson(['ip' => VISITOR_IP]);
});

test('forwarded headers from anyone else are ignored', function () {
    $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.7'])
        ->withHeaders(['X-Forwarded-For' => VISITOR_IP, 'X-Forwarded-Proto' => 'https'])
        ->getJson('/_test/client')
        ->assertJson(['ip' => '198.51.100.7', 'secure' => false]);
});

test('a forwarded host is not trusted, even from cloudflare', function () {
    $response = $this->withServerVariables(['REMOTE_ADDR' => CLOUDFLARE_EDGE_V4])
        ->withHeaders(['X-Forwarded-Host' => 'evil.example'])
        ->getJson('/_test/client');

    expect((string) $response->json('url'))->not->toContain('evil.example');
});

test('visitors behind one cloudflare edge have their own reset limit', function () {
    Notification::fake();

    $from = fn (string $visitor) => $this->withServerVariables(['REMOTE_ADDR' => CLOUDFLARE_EDGE_V4])
        ->withHeaders(['X-Forwarded-For' => $visitor]);

    for ($i = 1; $i <= 6; $i++) {
        $from(VISITOR_IP)->post('/forgot-password', ['email' => "nobody{$i}@example.com"])
            ->assertSessionHasNoErrors();
    }

    $from(VISITOR_IP)->post('/forgot-password', ['email' => 'nobody7@example.com'])
        ->assertSessionHasErrors(['email' => 'Too many attempts. Please try again in 1 minute.']);

    // Someone else on the same edge is not held back by that visitor.
    $this->app['session']->flush();
    $from('198.51.100.20')->post('/forgot-password', ['email' => 'other@example.com'])
        ->assertSessionHasNoErrors();
});

test('a forged header does not dodge the reset limit', function () {
    Notification::fake();

    for ($i = 1; $i <= 6; $i++) {
        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.7'])
            ->withHeaders(['X-Forwarded-For' => "203.0.113.{$i}"])
            ->post('/forgot-password', ['email' => "nobody{$i}@example.com"])
            ->assertSessionHasNoErrors();
    }

    $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.7'])
        ->withHeaders(['X-Forwarded-For' => '203.0.113.99'])
        ->post('/forgot-password', ['email' => 'nobody7@example.com'])
        ->assertSessionHasErrors(['email' => 'Too many attempts. Please try again in 1 minute.']);
});
