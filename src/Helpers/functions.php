<?php
/**
 * Global Helper Functions
 */

declare(strict_types=1);

use App\Core\Config;
use App\Core\Session;
use App\Core\Application;
use App\Core\View;

/**
 * Get configuration value
 */
function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}

/**
 * Get application instance or resolve from container
 */
function app(?string $abstract = null): mixed
{
    $instance = Application::getInstance();

    if ($abstract === null) {
        return $instance;
    }

    return $instance->resolve($abstract);
}

/**
 * Get base path
 */
function base_path(string $path = ''): string
{
    return BASE_PATH . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
}

/**
 * Get public path
 */
function public_path(string $path = ''): string
{
    return base_path('public' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
}

/**
 * Get storage path
 */
function storage_path(string $path = ''): string
{
    return base_path('storage' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
}

/**
 * Get views path
 */
function views_path(string $path = ''): string
{
    return base_path('views' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
}

/**
 * Generate URL
 */
function url(string $path = ''): string
{
    // Build base URL dynamically from current request
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Use SCRIPT_NAME to determine the base path reliably
    // SCRIPT_NAME is usually /project/public/index.php or /index.php
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $basePath = dirname($scriptName);

    // Normalize slashes (Windows might return backslashes)
    $basePath = str_replace('\\', '/', $basePath);

    // Remove trailing slash if strictly root
    $basePath = rtrim($basePath, '/');

    // Remove /public if it's already in the path (rare case correction)
    // Actually, we WANT /public if it's part of the real path. 
    // dirname('/jira/public/index.php') -> '/jira/public' which is correct.

    // Construct valid Base URL
    $baseUrl = "{$scheme}://{$host}{$basePath}";

    return $baseUrl . '/' . ltrim($path, '/');
}

/**
 * Get application base path (for JavaScript use)
 * Returns just the path part, not the full URL
 * Examples: '/cways_mis/public', '/', '/apps/jira/public'
 */
function basePath(): string
{
    // Use SCRIPT_NAME to determine the base path reliably
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $basePath = dirname($scriptName);

    // Normalize slashes
    $basePath = str_replace('\\', '/', $basePath);

    // Remove trailing slash if strictly root
    return rtrim($basePath, '/') ?: '/';
}

/**
 * Generate asset URL
 */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Generate avatar URL
 * Handles both relative and absolute avatar paths
 * FIXED: Handles /public/avatars/ paths that should be /uploads/avatars/
 */
function avatar(?string $avatarPath, string $defaultName = 'U'): string
{
    if (empty($avatarPath)) {
        // Return default avatar with initials
        return '';
    }

    // FIX: Handle incorrectly stored /public/avatars/ paths
    if (str_contains($avatarPath, '/public/avatars/')) {
        // Replace /public/avatars/ with /uploads/avatars/
        $avatarPath = str_replace('/public/avatars/', '/uploads/avatars/', $avatarPath);
    }

    // If it contains /uploads/, treat it as a local file and ensure dynamic URL generation
    // This fixes stale session data containing 'localhost' when accessed via LAN
    if (str_contains($avatarPath, '/uploads/')) {
        $pos = strpos($avatarPath, '/uploads/');
        // Extract from /uploads/ onwards (include /uploads/)
        $relativePath = substr($avatarPath, $pos);
        return url($relativePath);
    }

    // If avatar is already a full URL (external), use it as-is
    if (filter_var($avatarPath, FILTER_VALIDATE_URL)) {
        return $avatarPath;
    }

    // If it's a relative path starting with /uploads/ (redundant but safe)
    if (str_starts_with($avatarPath, '/uploads/')) {
        return url($avatarPath);
    }

    // If it's just a filename, assume it's in uploads/avatars/
    if (!str_contains($avatarPath, '/')) {
        return url("/uploads/avatars/$avatarPath");
    }

    // Otherwise, treat as relative path
    return url($avatarPath);
}

/**
 * Get avatar initials for default display
 */
function avatarInitials(string $name, string $email = ''): string
{
    $name = trim($name);
    if (empty($name)) {
        // Use email first part if name is empty
        $emailParts = explode('@', $email);
        return strtoupper(substr($emailParts[0] ?? 'U', 0, 1));
    }

    $nameParts = explode(' ', $name);
    if (count($nameParts) >= 2) {
        // Take first letter of first and last name
        return strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1));
    } else {
        // Take first two letters of single name
        return strtoupper(substr($name, 0, 2));
    }
}

/**
 * Render a view
 */
function view(string $name, array $data = []): string
{
    return View::render($name, $data);
}

/**
 * Redirect to URL
 */
function redirect(string $url, int $status = 302): never
{
    http_response_code($status);
    header("Location: $url");
    exit;
}

/**
 * Redirect back
 */
function back(): never
{
    $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
    redirect($referer);
}

/**
 * Get CSRF token
 */
function csrf_token(): string
{
    return Session::get('_csrf_token', '');
}

/**
 * Generate CSRF field
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Generate method field for PUT/PATCH/DELETE
 */
function method_field(string $method): string
{
    return '<input type="hidden" name="_method" value="' . e(strtoupper($method)) . '">';
}

/**
 * Get old input value
 */
function old(string $key, mixed $default = ''): mixed
{
    $oldInput = Session::getFlash('_old_input', []);
    return $oldInput[$key] ?? $default;
}

/**
 * Check if there are validation errors
 */
function has_error(string $field): bool
{
    $errors = Session::getFlash('_errors', []);
    return isset($errors[$field]);
}

/**
 * Get validation error message
 */
function error(string $field): string
{
    $errors = Session::getFlash('_errors', []);
    return $errors[$field][0] ?? '';
}

/**
 * Get all validation errors
 */
function errors(): array
{
    return Session::getFlash('_errors', []);
}

/**
 * Alias for has_error (camelCase)
 */
function hasError(string $field): bool
{
    return has_error($field);
}

/**
 * Alias for error (camelCase) - getError
 */
function getError(string $field): string
{
    return error($field);
}

/**
 * Escape HTML entities
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8', true);
}

/**
 * Escape for JavaScript
 */
function ejs(?string $value): string
{
    return json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
}

/**
 * Check if user is authenticated
 */
function auth(): ?array
{
    return Session::get('_user');
}

/**
 * Check if user has permission
 */
function can(string $permission, ?int $projectId = null): bool
{
    $user = auth();
    if (!$user) {
        return false;
    }

    return app('auth')->can($permission, $projectId);
}

/**
 * Get current user ID
 */
function user_id(): ?int
{
    $user = auth();
    return $user['id'] ?? null;
}

/**
 * Flash a message to session
 */
function flash(string $key, mixed $value): void
{
    Session::flash($key, $value);
}

/**
 * Get current timestamp
 */
function now(): DateTimeImmutable
{
    return new DateTimeImmutable('now', new DateTimeZone(config('app.timezone', 'UTC')));
}

/**
 * Format date
 */
function format_date(?string $date, string $format = 'M j, Y'): string
{
    if (!$date) {
        return '';
    }

    try {
        $dt = new DateTime($date, new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone(config('app.timezone', 'UTC')));
        return $dt->format($format);
    } catch (Exception) {
        return '';
    }
}

/**
 * Format datetime
 */
function format_datetime(?string $datetime, string $format = 'M j, Y g:i A'): string
{
    return format_date($datetime, $format);
}

/**
 * Format time spent (minutes to Jira-style string)
 */
function format_time($minutes): string
{
    $minutes = (int) $minutes;
    if ($minutes <= 0) {
        return '0m';
    }

    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    $days = floor($hours / 8); // Standard Jira day is 8h
    $hours = $hours % 8;

    $parts = [];
    if ($days > 0)
        $parts[] = $days . 'd';
    if ($hours > 0)
        $parts[] = $hours . 'h';
    if ($mins > 0)
        $parts[] = $mins . 'm';

    return implode(' ', $parts);
}

/**
 * Format time ago
 */
function time_ago(?string $datetime): string
{
    if (!$datetime) {
        return '';
    }

    try {
        $time = new DateTime($datetime, new DateTimeZone('UTC'));
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $diff = $now->getTimestamp() - $time->getTimestamp();

        if ($diff < 60) {
            return 'just now';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 2592000) {
            $weeks = floor($diff / 604800);
            return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
        } else {
            return format_date($datetime);
        }
    } catch (Exception) {
        return '';
    }
}

/**
 * Generate a random string
 */
function random_string(int $length = 32): string
{
    return bin2hex(random_bytes($length / 2));
}

/**
 * Generate UUID v4
 */
function uuid(): string
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * Dump and die
 */
function dd(mixed ...$vars): never
{
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
    }

    echo '<pre style="background:#1e1e1e;color:#dcdcdc;padding:15px;margin:10px;border-radius:5px;font-family:monospace;font-size:13px;overflow:auto;">';
    foreach ($vars as $var) {
        var_dump($var);
        echo "\n";
    }
    echo '</pre>';
    exit(1);
}

/**
 * Dump without dying
 */
function dump(mixed ...$vars): void
{
    echo '<pre style="background:#1e1e1e;color:#dcdcdc;padding:15px;margin:10px;border-radius:5px;font-family:monospace;font-size:13px;overflow:auto;">';
    foreach ($vars as $var) {
        var_dump($var);
        echo "\n";
    }
    echo '</pre>';
}

/**
 * Abort with HTTP status
 */
function abort(int $code, string $message = ''): never
{
    http_response_code($code);

    $titles = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        419 => 'Page Expired',
        422 => 'Unprocessable Entity',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    ];

    $title = $titles[$code] ?? 'Error';

    // Check if this is an API request
    if (wants_json() || is_api_request()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error' => $title,
            'message' => $message,
            'status' => $code
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else if (config('app.debug') && $message) {
        echo "<h1>$code $title</h1><p>$message</p>";
    } else {
        $errorView = views_path("errors/$code.php");
        if (file_exists($errorView)) {
            // Make message available to error template
            include $errorView;
        } else {
            echo "<h1>$code $title</h1>";
            if (!config('app.debug') && $message) {
                echo "<p>$message</p>";
            }
        }
    }

    exit;
}

/**
 * Get client IP address
 */
function client_ip(): string
{
    $headers = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR',
    ];

    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ips = explode(',', $_SERVER[$header]);
            $ip = trim($ips[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }

    return '0.0.0.0';
}

/**
 * Format file size
 */
function format_bytes(int $bytes, int $precision = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Slugify a string
 */
function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);

    return $text ?: 'n-a';
}

/**
 * Truncate text
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length - mb_strlen($suffix)) . $suffix;
}

/**
 * Parse Markdown to HTML (basic)
 */
function markdown(string $text): string
{
    // Basic markdown parsing - for production, use a proper library
    $text = e($text);

    // Bold
    $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/__(.+?)__/s', '<strong>$1</strong>', $text);

    // Italic
    $text = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $text);
    $text = preg_replace('/_(.+?)_/s', '<em>$1</em>', $text);

    // Code
    $text = preg_replace('/`(.+?)`/', '<code>$1</code>', $text);

    // Links
    $text = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $text);

    // Line breaks
    $text = nl2br($text);

    return $text;
}

/**
 * Convert array to JSON response
 */
function json(mixed $data, int $status = 200): never
{
    // Clear any output buffer to prevent mixing content types
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8', true);
    header('Cache-Control: no-cache, no-store, must-revalidate', true);
    header('Pragma: no-cache', true);
    header('Expires: 0', true);

    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!headers_sent()) {
        header('Content-Length: ' . strlen($json), true);
    }

    echo $json;
    exit;
}

/**
 * Check if request is AJAX
 */
function is_ajax(): bool
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Check if request wants JSON
 */
function wants_json(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    return str_contains($accept, 'application/json');
}

/**
 * Check if this is an API request (based on URI path)
 */
function is_api_request(): bool
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    return str_contains($uri, '/api/');
}

/**
 * Get request method
 */
function request_method(): string
{
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    // Support method override
    if ($method === 'POST' && isset($_POST['_method'])) {
        $method = strtoupper($_POST['_method']);
    }

    return $method;
}

/**
 * Get translation string
 */
function __(string $key, array $replace = []): string
{
    return app('translator')->get($key, $replace);
}

/**
 * Pluralize a word
 */
function pluralize(int $count, string $singular, string $plural): string
{
    return $count === 1 ? $singular : $plural;
}

/**
 * Sanitize issue data for JSON API responses
 * Removes sensitive PII fields like emails
 */
function sanitize_issue_for_json(?array $issue): ?array
{
    if (!$issue) {
        return null;
    }

    $sanitized = $issue;

    // Remove email fields from user-related data
    unset($sanitized['reporter_email']);
    unset($sanitized['assignee_email']);

    // Sanitize comments
    if (!empty($sanitized['comments']) && is_array($sanitized['comments'])) {
        $sanitized['comments'] = array_map(function ($comment) {
            $commentSanitized = $comment;
            if (!empty($commentSanitized['user']) && is_array($commentSanitized['user'])) {
                unset($commentSanitized['user']['email']);
            }
            unset($commentSanitized['email']);
            return $commentSanitized;
        }, $sanitized['comments']);
    }

    return $sanitized;
}

/**
 * Sanitize multiple issues for JSON API responses
 */
function sanitize_issues_for_json(array $issues): array
{
    return array_map('sanitize_issue_for_json', $issues);
}
/**
 * Get contrast color (black or white) based on background hex color
 */
function contrast_color(string $hexColor): string
{
    // Remove # if present
    $hex = ltrim($hexColor, '#');

    // Convert to RGB
    if (strlen($hex) === 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }

    // Calculate luminance (perceived brightness)
    // Formula: L = 0.299*R + 0.587*G + 0.114*B
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

    // Return black for light backgrounds, white for dark backgrounds
    return $luminance > 0.5 ? '#000000' : '#ffffff';
}
