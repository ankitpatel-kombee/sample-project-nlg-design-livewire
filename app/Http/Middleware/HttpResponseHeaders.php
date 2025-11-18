<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class HttpResponseHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! App::environment('local')) {
            // Set Headers
            $response->headers->set('X-Frame-Options', 'DENY', false);
            $response->headers->set('X-Content-Type-Options', 'nosniff', false);
            $response->headers->set('Content-Security-Policy', 'policy', false);
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubdomains; preload',
                false
            );

            // Set Content-Security-Policy without nonce and with replace=true
            $csp = "default-src 'self'; " .
                "script-src 'self' 'unsafe-eval' 'unsafe-inline'; " .
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
                "style-src-elem 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
                "img-src 'self' data:; " .
                "font-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com; " .
                "connect-src 'self' https://ka-f.fontawesome.com; " .
                "base-uri 'self'; " .
                "frame-ancestors 'self'; " .
                "form-action 'self';";

            $response->headers->set('Content-Security-Policy', $csp, true);

            $response->headers->set(
                'Permissions-Policy',
                'geolocation=(self)'
            );
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

            // Unset Headers
            /* header_remove('X-Powered-By');
            header_remove('Server'); */
        }

        return $response;
    }
}
