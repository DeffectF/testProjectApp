<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class TenantIsolation
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $parts = explode('.', $host);
        $tenantIdentifier = null;

        if (count($parts) > 2) {
            $tenantIdentifier = $parts[0];
        }
        if ($request->hasHeader('X-Tenant')) {
            $tenantIdentifier = $request->header('X-Tenant');
        }
        if (!$tenantIdentifier) {
            abort(403, 'Tenant not specified');
        }
        $tenant = DB::table('tenants')->where('identifier', $tenantIdentifier)->first();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        app()->instance('tenant', $tenant);

        Config::set('database.connections.tenant', [
            'driver' => 'mysql',
            'host' => $tenant->db_host,
            'port' => $tenant->db_port,
            'database' => $tenant->db_name,
            'username' => $tenant->db_user,
            'password' => $tenant->db_password,

        ]);

        DB::purge('tenant');
        DB::setDefaultConnection('tenant');

        return $next($request);
    }
}
