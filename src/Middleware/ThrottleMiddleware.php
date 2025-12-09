<?php
/**
 * Rate Limiting Middleware
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Cache;

class ThrottleMiddleware
{
    /**
     * Handle the request
     */
    public function handle(Request $request, callable $next, mixed $maxAttempts = 60, mixed $decayMinutes = 1): mixed
    {
        // Cast to int in case middleware parameters come as strings
        $maxAttempts = (int) $maxAttempts;
        $decayMinutes = (int) $decayMinutes;
        
        $key = $this->resolveRequestSignature($request);
        $cache = new Cache();

        $attempts = (int) $cache->get($key, 0);

        if ($attempts >= $maxAttempts) {
            $retryAfter = $cache->get($key . '_reset', 0) - time();
            
            header('X-RateLimit-Limit: ' . $maxAttempts);
            header('X-RateLimit-Remaining: 0');
            header('Retry-After: ' . max(0, $retryAfter));

            json([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => max(0, $retryAfter),
            ], 429);
        }

        // Increment attempts
        $cache->put($key, $attempts + 1, $decayMinutes * 60);
        
        if ($attempts === 0) {
            $cache->put($key . '_reset', time() + ($decayMinutes * 60), $decayMinutes * 60);
        }

        // Add rate limit headers
        header('X-RateLimit-Limit: ' . $maxAttempts);
        header('X-RateLimit-Remaining: ' . ($maxAttempts - $attempts - 1));

        return $next($request);
    }

    /**
     * Generate unique request signature
     */
    private function resolveRequestSignature(Request $request): string
    {
        // Use user ID if authenticated, otherwise IP
        $user = auth();
        $identifier = $user ? 'user_' . $user['id'] : 'ip_' . $request->ip();

        return 'throttle_' . sha1($identifier . '|' . $request->path());
    }
}
