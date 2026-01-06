<?php
/**
 * API Authentication Middleware (JWT/PAT)
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\JWT;
use App\Core\Database;
use App\Core\Session;

class ApiMiddleware
{
    /**
     * Handle the request
     */
    public function handle(Request $request, callable $next): mixed
    {
        $user = $this->authenticate($request);

        if (!$user) {
            json([
                'error' => 'Unauthenticated',
                'message' => 'Invalid or missing authentication token',
            ], 401);
        }

        // Store user in request for later use
        $GLOBALS['api_user'] = $user;

        return $next($request);
    }

    /**
     * Authenticate request
     */
    private function authenticate(Request $request): ?array
    {
        // Try Bearer token (JWT)
        $token = $request->bearerToken();
        if ($token) {
            return $this->authenticateJWT($token);
        }

        // Try API key header
        $apiKey = $request->header('X-API-Key');
        if ($apiKey) {
            return $this->authenticatePAT($apiKey);
        }

        // Try query parameter (for specific use cases like file downloads)
        $queryToken = $request->query('api_token');
        if ($queryToken) {
            return $this->authenticatePAT($queryToken);
        }

        // Try session-based authentication (for AJAX calls from web pages)
        $user = $this->authenticateSession();
        if ($user) {
            return $user;
        }

        return null;
    }

    /**
     * Authenticate with JWT
     */
    private function authenticateJWT(string $token): ?array
    {
        $payload = JWT::decode($token);
        if (!$payload) {
            return null;
        }

        // Get user from database
        $user = Database::selectOne(
            "SELECT id, email, first_name, last_name, is_active 
             FROM users WHERE id = ? AND is_active = 1",
            [$payload['sub'] ?? $payload['user_id'] ?? 0]
        );

        if (!$user) {
            return null;
        }

        return [
            'id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'token_type' => 'jwt',
        ];
    }

    /**
     * Authenticate with Personal Access Token
     */
    private function authenticatePAT(string $token): ?array
    {
        $user = JWT::validatePAT($token);
        if (!$user) {
            return null;
        }

        $user['token_type'] = 'pat';
        return $user;
    }

    /**
     * Authenticate with session (for AJAX calls from web pages)
     */
    private function authenticateSession(): ?array
    {
        $user = Session::user();
        if (!$user || !($user['is_active'] ?? true)) {
            return null;
        }

        return [
            'id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'token_type' => 'session',
        ];
    }
}
