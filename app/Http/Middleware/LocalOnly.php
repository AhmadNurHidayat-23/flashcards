<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocalOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        // Forwarded headers must never grant access to anonymous management.
        abort_unless(in_array($request->server('REMOTE_ADDR'), [
            '127.0.0.1', '::1', '::ffff:127.0.0.1',
        ], true), 403, 'Flashcards hanya tersedia melalui komputer lokal.');

        return $next($request);
    }
}
