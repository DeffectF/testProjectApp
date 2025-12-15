<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    public function handle($request, Closure $next)
    {
        if ($request->header('X-API-Key') !== 'secret') {
            return response()->json(['message' => 'Invalid API key'], 401);
        }

        return $next($request);
    }

}
