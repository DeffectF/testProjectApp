<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiVersionMiddleware
{
    protected array $supportedVersions = ['v1', 'v2'];
    protected string $fallbackVersion = 'v2';

    public function handle(Request $request, Closure $next)
    {
        $acceptHeader = $request->header('Accept');

        if (preg_match('/application\/vnd\.api\.v([0-9]+)\+json/', $acceptHeader, $matches)) {
            $version = 'v' . $matches[1];

            if (in_array($version, $this->supportedVersions)) {
                $request->attributes->set('api_version', $version);
            } else {
                $request->attributes->set('api_version', $this->fallbackVersion);
            }
        } else {
            $request->attributes->set('api_version', $this->fallbackVersion);
        }

        return $next($request);
    }

    public function index(Request $request): void
    {
        $request->get('api_version');

    }
}
