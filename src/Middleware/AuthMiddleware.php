<?php
/**
 * Authentication Middleware
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Core\View;

class AuthMiddleware
{
    /**
     * Handle the request
     */
    public function handle(Request $request, callable $next): mixed
    {
        if (!Session::check()) {
            // Store intended URL for redirect after login
            Session::setIntended($request->fullUrl());

            if ($request->wantsJson() || $request->isAjax()) {
                json(['error' => 'Unauthenticated'], 401);
            }

            Session::flash('error', 'Please log in to continue.');
            redirect(url('/login'));
        }

        // Check if session is still valid
        $user = Session::user();
        if (!$user || !($user['is_active'] ?? true)) {
            Session::logout();
            
            if ($request->wantsJson() || $request->isAjax()) {
                json(['error' => 'Session expired'], 401);
            }

            Session::flash('error', 'Your session has expired. Please log in again.');
            redirect(url('/login'));
        }

        // Share user notifications with all views
        $this->shareNotifications($user['id']);

        return $next($request);
    }

    /**
     * Share notifications globally with views
     */
    private function shareNotifications(int $userId): void
    {
        try {
            // Only fetch unread notifications for the navbar dropdown
            $notifications = Database::select(
                "SELECT * FROM notifications
                 WHERE user_id = ? AND read_at IS NULL
                 ORDER BY created_at DESC
                 LIMIT 10",
                [$userId]
            );
            
            // Parse JSON data for each notification
            foreach ($notifications as &$notification) {
                if (!empty($notification['data'])) {
                    $notification['data'] = json_decode($notification['data'], true) ?? [];
                } else {
                    $notification['data'] = [];
                }
            }
            
            View::share('notifications', $notifications);
        } catch (\Exception $e) {
            // If notifications table doesn't exist or other error, share empty array
            View::share('notifications', []);
        }
    }
}
