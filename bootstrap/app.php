<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Card 5533: HSTS, frame-ancestors, Referrer-Policy and friends on
        // every response, error pages included.
        $middleware->append(SecurityHeaders::class);

        // Card 5703: Cloudflare's ranges are in config/trustedproxy.php. Not
        // X-Forwarded-Host: Cloudflare passes a visitor's own one through.
        $middleware->trustProxies(headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO);

        $middleware->web(append: [
            // Card 5532: a password change or reset signs out every other
            // session (and remember-me cookie) of that account.
            AuthenticateSession::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
