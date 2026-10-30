<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     * Prevents browser caching on protected and dynamic pages so back-button does not restore stale session views.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (isset($response->headers)) {
            // Standard HTTP Security Headers (Clickjacking, MIME-confusion, and Referrer privacy)
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

            // Apply no-cache headers to dynamic web pages to prevent browser from caching authenticated session states
            if (!($response instanceof BinaryFileResponse || $response instanceof StreamedResponse)) {
                $response->headers->set('Cache-Control', 'no-cache, private, no-store, must-revalidate, max-age=0, post-check=0, pre-check=0');
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
            }
        }

        return $response;
    }
}
