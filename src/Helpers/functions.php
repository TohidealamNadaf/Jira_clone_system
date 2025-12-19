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
    // This fixes the issue where hardcoded URLs break when accessing from different IPs
    
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Get the request URI to determine the base path
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    
    // Determine the base path by checking if we're in the public subdirectory
    // Common patterns:
    // - /jira_clone_system/public/login -> base: /jira_clone_system/public
    // - /login -> base: /
    // - /index.php -> base: /
    
    $basePath = '';
    
    // Check if the request contains subdirectory paths
    if (preg_match('#^(/[^/]+/[^/]+)(/|$)#', $requestUri, $matches)) {
        // We have a path like /jira_clone_system/public or similar
        $basePath = $matches[1];
    } elseif (preg_match('#^/[^/\.]+(/|$)#', $requestUri) && strpos($requestUri, '/index.php') === false) {
        // We have a single directory like /jira_clone_system but not the public subdirectory
        // Try to detect if public is expected
        $parts = explode('/', trim($requestUri, '/'));
        if (count($parts) > 0 && !in_array($parts[0], ['login', 'dashboard', 'projects', 'issues', 'api', 'admin', 'search', 'reports'])) {
            $basePath = '/' . $parts[0];
            // Check if /public should be added
            if (is_dir($_SERVER['DOCUMENT_ROOT'] . $basePath . '/public')) {
                $basePath .= '/public';
            }
        }
    }
    
    $baseUrl = "{$scheme}://{$host}{$basePath}";
    $baseUrl = rtrim($baseUrl, '/');
    
    return $baseUrl . '/' . ltrim($path, '/');
}

/**
 * Generate asset URL
 */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
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
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
        $sanitized['comments'] = array_map(function($comment) {
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
