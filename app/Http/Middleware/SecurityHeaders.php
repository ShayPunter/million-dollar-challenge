<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Card 5533: security headers on every response, sent by the app rather
 * than Forge's nginx (which adds X-Frame-Options and nosniff to 2xx/3xx
 * responses only, and no HSTS at all).
 *
 * Global middleware, so error pages (404, 419, 500) get them too.
 *
 * - No X-Frame-Options here: nginx already sends SAMEORIGIN, and two values
 *   would conflict. CSP frame-ancestors overrides it in every current browser.
 * - frame-ancestors 'self': the home page frames a live grid
 *   (/grid/shaypunter) from this site. Other sites still can't frame it, as
 *   with nginx's SAMEORIGIN today.
 * - No full CSP yet: Ziggy's @routes is an inline script, so script-src would
 *   need a nonce on every page. Only the directives that need no allow-list
 *   are set.
 */
class SecurityHeaders
{
    public const CONTENT_SECURITY_POLICY = "base-uri 'self'; object-src 'none'; frame-ancestors 'self'";

    public const PERMISSIONS_POLICY = 'camera=(), microphone=(), geolocation=(), payment=(), usb=()';

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // A year, no includeSubDomains and no preload yet (card 5533).
        // Browsers ignore it on plain-http responses, so local dev is unaffected.
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        $response->headers->set('Content-Security-Policy', self::CONTENT_SECURITY_POLICY);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', self::PERMISSIONS_POLICY);

        return $response;
    }
}
