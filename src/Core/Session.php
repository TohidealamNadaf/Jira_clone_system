<?php
/**
 * Session Handler
 */

declare(strict_types=1);

namespace App\Core;

class Session
{
    private static bool $started = false;

    /**
     * Start the session
     */
    public function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $config = config('session', []);

        // Set session cookie parameters
        session_set_cookie_params([
            'lifetime' => $config['lifetime'] ?? 7200,
            'path' => $config['path'] ?? '/',
            'domain' => $config['domain'] ?? '',
            'secure' => $config['secure'] ?? false,
            'httponly' => $config['httponly'] ?? true,
            'samesite' => $config['samesite'] ?? 'Lax',
        ]);

        // Set session name
        session_name($config['name'] ?? 'jira_session');

        // Start session
        session_start();
        self::$started = true;

        // Regenerate ID periodically for security
        $this->regenerateIfNeeded();

        // Process flash data
        $this->processFlash();
    }

    /**
     * Regenerate session ID if needed
     */
    private function regenerateIfNeeded(): void
    {
        $regenerateInterval = 300; // 5 minutes
        $lastRegenerate = $_SESSION['_last_regenerate'] ?? 0;

        if (time() - $lastRegenerate > $regenerateInterval) {
            session_regenerate_id(true);
            $_SESSION['_last_regenerate'] = time();
        }
    }

    /**
     * Process flash data
     */
    private function processFlash(): void
    {
        // Remove old flash data
        if (isset($_SESSION['_flash_old'])) {
            foreach ($_SESSION['_flash_old'] as $key) {
                unset($_SESSION[$key]);
            }
        }

        // Move new flash to old
        $_SESSION['_flash_old'] = $_SESSION['_flash_new'] ?? [];
        $_SESSION['_flash_new'] = [];
    }

    /**
     * Get session value
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set session value
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Check if session has key
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session value
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Get all session data
     */
    public static function all(): array
    {
        return $_SESSION;
    }

    /**
     * Clear all session data
     */
    public static function clear(): void
    {
        $_SESSION = [];
    }

    /**
     * Destroy session
     */
    public static function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        self::$started = false;
    }

    /**
     * Regenerate session ID
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
        $_SESSION['_last_regenerate'] = time();
    }

    /**
     * Flash data for next request
     */
    public static function flash(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
        $_SESSION['_flash_new'][] = $key;
    }

    /**
     * Get flash data
     */
    public static function getFlash(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Keep flash data for another request
     */
    public static function reflash(): void
    {
        $_SESSION['_flash_new'] = array_merge(
            $_SESSION['_flash_new'] ?? [],
            $_SESSION['_flash_old'] ?? []
        );
        $_SESSION['_flash_old'] = [];
    }

    /**
     * Keep specific flash keys
     */
    public static function keep(array $keys): void
    {
        foreach ($keys as $key) {
            if (isset($_SESSION[$key])) {
                $_SESSION['_flash_new'][] = $key;
            }
        }
    }

    /**
     * Get session ID
     */
    public static function id(): string
    {
        return session_id();
    }

    /**
     * Set user data (for authentication)
     */
    public static function setUser(array $user): void
    {
        self::regenerate();
        self::set('_user', $user);
        self::set('_auth_time', time());
    }

    /**
     * Get authenticated user
     */
    public static function user(): ?array
    {
        return self::get('_user');
    }

    /**
     * Check if user is authenticated
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * Logout user
     */
    public static function logout(): void
    {
        self::remove('_user');
        self::remove('_auth_time');
        self::regenerate();
    }

    /**
     * Store intended URL for redirect after login
     */
    public static function setIntended(string $url): void
    {
        self::set('_intended_url', $url);
    }

    /**
     * Get and clear intended URL
     */
    public static function pullIntended(string $default = '/'): string
    {
        $url = self::get('_intended_url', $default);
        self::remove('_intended_url');
        return $url;
    }
}
