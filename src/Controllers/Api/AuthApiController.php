<?php
/**
 * Auth API Controller
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Database;
use App\Core\JWT;
use App\Services\AuthService;

class AuthApiController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function login(Request $request): never
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = Database::selectOne(
            "SELECT * FROM users WHERE email = ? AND is_active = 1",
            [$data['email']]
        );

        if (!$user) {
            $this->json([
                'error' => 'Invalid credentials',
                'message' => 'The provided email or password is incorrect.',
            ], 401);
        }

        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $this->json([
                'error' => 'Account locked',
                'message' => 'Account is temporarily locked. Please try again later.',
                'locked_until' => $user['locked_until'],
            ], 423);
        }

        if (!password_verify($data['password'], $user['password_hash'])) {
            Database::update('users', [
                'failed_login_attempts' => ($user['failed_login_attempts'] ?? 0) + 1,
            ], 'id = ?', [$user['id']]);

            $this->json([
                'error' => 'Invalid credentials',
                'message' => 'The provided email or password is incorrect.',
            ], 401);
        }

        Database::update('users', [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => client_ip(),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ], 'id = ?', [$user['id']]);

        $accessToken = JWT::encode([
            'sub' => $user['id'],
            'email' => $user['email'],
            'type' => 'access',
        ]);

        $refreshToken = JWT::encode([
            'sub' => $user['id'],
            'type' => 'refresh',
        ], config('jwt.refresh_ttl', 60 * 24 * 7));

        Database::insert('audit_logs', [
            'user_id' => $user['id'],
            'action' => 'api_login',
            'entity_type' => 'user',
            'entity_id' => $user['id'],
            'ip_address' => client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);

        $this->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl', 60) * 60,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'display_name' => $user['display_name'],
                'avatar' => $user['avatar'],
            ],
        ]);
    }

    public function refresh(Request $request): never
    {
        $refreshToken = $request->input('refresh_token');

        if (!$refreshToken) {
            $this->json([
                'error' => 'Missing token',
                'message' => 'Refresh token is required.',
            ], 400);
        }

        $payload = JWT::decode($refreshToken);

        if (!$payload || ($payload['type'] ?? '') !== 'refresh') {
            $this->json([
                'error' => 'Invalid token',
                'message' => 'The refresh token is invalid or expired.',
            ], 401);
        }

        $user = Database::selectOne(
            "SELECT * FROM users WHERE id = ? AND is_active = 1",
            [$payload['sub']]
        );

        if (!$user) {
            $this->json([
                'error' => 'User not found',
                'message' => 'The user associated with this token no longer exists.',
            ], 401);
        }

        $accessToken = JWT::encode([
            'sub' => $user['id'],
            'email' => $user['email'],
            'type' => 'access',
        ]);

        $newRefreshToken = JWT::encode([
            'sub' => $user['id'],
            'type' => 'refresh',
        ], config('jwt.refresh_ttl', 60 * 24 * 7));

        $this->json([
            'access_token' => $accessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl', 60) * 60,
        ]);
    }

    public function logout(Request $request): never
    {
        $user = $GLOBALS['api_user'] ?? null;

        if ($user) {
            Database::insert('audit_logs', [
                'user_id' => $user['id'],
                'action' => 'api_logout',
                'entity_type' => 'user',
                'entity_id' => $user['id'],
                'ip_address' => client_ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);
        }

        $this->json([
            'message' => 'Successfully logged out.',
        ]);
    }
}
