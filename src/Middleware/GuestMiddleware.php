<?php
/**
 * Guest Middleware (Not Authenticated)
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;

class GuestMiddleware
{
    /**
     * Handle the request
     */
    public function handle(Request $request, callable $next): mixed
    {
        if (Session::check()) {
            redirect(url('/dashboard'));
        }

        return $next($request);
    }
}
