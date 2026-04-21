<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // ── Step 1: Validate Auth Key ──────────────────────────
        $providedKey = $request->header('X-Auth-Key');
        $validKey    = config('api.auth_key');

        if (empty($providedKey)) {
            return $this->error('AUTH_001', 'Authentication key is missing.', 401);
        }

        if (strlen($providedKey) !== 25) {
            return $this->error('AUTH_001', 'Authentication key must be exactly 25 characters.', 401);
        }

        if (!hash_equals($validKey, $providedKey)) {
            return $this->error('AUTH_001', 'Invalid authentication key.', 401);
        }

        // ── Step 2: Validate Organization Name ────────────────
        $providedOrg = $request->header('X-Organization');
        $validOrg    = config('api.org_name');

        if (empty($providedOrg)) {
            return $this->error('AUTH_002', 'Organization name header is missing.', 403);
        }

        if ($providedOrg !== $validOrg) {
            return $this->error('AUTH_002', 'Invalid organization name. Expected: "Door Soft".', 403);
        }

        // ── Step 3: Rate Limiting (5 req/min per IP) ──────────
        $ip       = $request->ip();
        $cacheKey = 'api_rate_' . md5($ip);
        $limit    = config('api.rate_limit', 5);
        $hits     = Cache::get($cacheKey, 0);

        if ($hits >= $limit) {
            return $this->error(
                'RATE_001',
                'Rate limit exceeded. Maximum ' . $limit . ' requests per minute allowed.',
                429,
                ['Retry-After' => 60]
            );
        }

        // Increment hit count, expires in 60 seconds
        Cache::put($cacheKey, $hits + 1, 60);

        // Add rate limit headers to response
        $response = $next($request);
        $response->headers->set('X-RateLimit-Limit',     $limit);
        $response->headers->set('X-RateLimit-Remaining', max(0, $limit - $hits - 1));

        return $response;
    }

    private function error(string $code, string $message, int $status, array $headers = []): Response
    {
        return response()->json([
            'success'    => false,
            'error_code' => $code,
            'message'    => $message,
        ], $status, $headers);
    }
}