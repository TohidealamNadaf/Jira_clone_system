<?php
/**
 * Admin Role Middleware
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;

class AdminMiddleware
{
    /**
     * Handle the request
     */
    public function handle(Request $request, callable $next): mixed
    {
        $user = Session::user();

        if (!$user) {
            if ($request->wantsJson() || $request->isAjax()) {
                json(['error' => 'Unauthenticated'], 401);
            }
            redirect(url('/login'));
        }

        // Check if user has admin role
        $isAdmin = ($user['role_slug'] ?? '') === 'admin' || ($user['is_admin'] ?? false);

        if (!$isAdmin) {
            if ($request->wantsJson() || $request->isAjax()) {
                json(['error' => 'Forbidden'], 403);
            }
            abort(403, 'Access denied. Administrator privileges required.');
        }

        return $next($request);
    }
}
