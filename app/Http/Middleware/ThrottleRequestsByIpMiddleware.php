<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Redis;
use Symfony\Component\HttpFoundation\Response;

class ThrottleRequestsByIpMiddleware
{
    protected array $limits = [
        'api/v1/*' => 10,
        'api/v2/*' => 20,
    ];

    public function handle($request, Closure $next)
    {
        $ipAddress = $request->ip();
        $currentPath = $request->path();
        $next = $next($request);

        foreach ($this->limits as $pattern => $limitPerMinute) {
            if (fnmatch($pattern, $currentPath)) {
                return $this->checkRateLimit($ipAddress, $next, $currentPath, $limitPerMinute);
            }
        }
        return $next($request);
    }

    public function checkRateLimit(string $ipAddress, string $path, int $limitPerMinute, $next): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        $key = "throttle:$ipAddress:$path";
        $remainingSecondsInWindow = 60 - time() % 60;
        $count = Redis::incr($key);
        Redis::expire($key, 60);

        if ($count > $limitPerMinute) {
            return response()->json([
                'message' => 'Too Many Requests',
                'retry_after' => $remainingSecondsInWindow,
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }
        return response()
            ->make($next(request()))
            ->header('X-Rate limit-Limit', $limitPerMinute)
            ->header('X-Rate limit-Remaining', max(0, $limitPerMinute - $count));
    }
}
