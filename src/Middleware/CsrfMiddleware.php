<?php
/**
 * CSRF Protection Middleware
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;

class CsrfMiddleware
{
    /**
     * Methods that require CSRF verification
     */
    private array $verifyMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * URIs to exclude from CSRF verification
     */
    private array $except = [
        '/api/*',
        '/webhooks/*',
    ];

    /**
     * Handle the request
     */
    public function handle(Request $request, callable $next): mixed
    {
        if ($this->shouldVerify($request)) {
            $token = $request->input('_csrf_token') ?? $request->header('X-CSRF-TOKEN');
            $sessionToken = Session::get('_csrf_token');

            if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
                if ($request->wantsJson() || $request->isAjax()) {
                    json(['error' => 'CSRF token mismatch'], 419);
                }

                Session::flash('error', 'Page expired. Please try again.');
                back();
            }
        }

        return $next($request);
    }

    /**
     * Check if request should be verified
     */
    private function shouldVerify(Request $request): bool
    {
        if (!in_array($request->method(), $this->verifyMethods)) {
            return false;
        }

        $path = $request->path();
        foreach ($this->except as $pattern) {
            if ($this->matchesPattern($path, $pattern)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if path matches pattern
     */
    private function matchesPattern(string $path, string $pattern): bool
    {
        $pattern = preg_quote($pattern, '#');
        $pattern = str_replace('\*', '.*', $pattern);
        return preg_match('#^' . $pattern . '$#', $path) === 1;
    }
}
