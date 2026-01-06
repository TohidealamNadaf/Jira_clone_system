<?php
/**
 * HTTP Request Handler
 */

declare(strict_types=1);

namespace App\Core;

class Request
{
    private array $params;
    private array $query;
    private array $post;
    private array $files;
    private array $server;
    private array $headers;
    private ?array $json = null;

    public function __construct(array $params = [])
    {
        $this->params = $params;
        $this->query = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->headers = $this->parseHeaders();
    }

    /**
     * Parse HTTP headers from server variables
     */
    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', substr($key, 5));
                $headers[strtolower($name)] = $value;
            }
        }
        return $headers;
    }

    /**
     * Get route parameter
     */
    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * Get all route parameters
     */
    public function params(): array
    {
        return $this->params;
    }

    /**
     * Get query parameter (GET)
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * Get all query parameters
     */
    public function queryAll(): array
    {
        return $this->query;
    }

    /**
     * Get POST parameter
     */
    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Get all POST parameters
     */
    public function postAll(): array
    {
        return $this->post;
    }

    /**
     * Get input from any source (POST, GET, JSON)
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] 
            ?? $this->query[$key] 
            ?? $this->json()[$key] 
            ?? $default;
    }

    /**
     * Get all input data
     */
    public function all(): array
    {
        return array_merge($this->query, $this->post, $this->json());
    }

    /**
     * Get only specified keys
     */
    public function only(array $keys): array
    {
        $all = $this->all();
        return array_intersect_key($all, array_flip($keys));
    }

    /**
     * Get all except specified keys
     */
    public function except(array $keys): array
    {
        $all = $this->all();
        return array_diff_key($all, array_flip($keys));
    }

    /**
     * Check if input has key
     */
    public function has(string $key): bool
    {
        return isset($this->all()[$key]);
    }

    /**
     * Check if input has non-empty value
     */
    public function filled(string $key): bool
    {
        $value = $this->input($key);
        return $value !== null && $value !== '';
    }

    /**
     * Get JSON body
     */
    public function json(): array
    {
        if ($this->json === null) {
            $content = file_get_contents('php://input');
            $this->json = json_decode($content, true) ?? [];
        }
        return $this->json;
    }

    /**
     * Get uploaded file
     */
    public function file(string $key): ?array
    {
        if (!isset($this->files[$key]) || $this->files[$key]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        return $this->files[$key];
    }

    /**
     * Check if file was uploaded
     */
    public function hasFile(string $key): bool
    {
        return $this->file($key) !== null;
    }

    /**
     * Get all uploaded files
     */
    public function files(): array
    {
        return $this->files;
    }

    /**
     * Get header value
     */
    public function header(string $key, mixed $default = null): mixed
    {
        return $this->headers[strtolower($key)] ?? $default;
    }

    /**
     * Get all headers
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Get bearer token from Authorization header
     */
    public function bearerToken(): ?string
    {
        $auth = $this->header('authorization', '');
        if (str_starts_with($auth, 'Bearer ')) {
            return substr($auth, 7);
        }
        return null;
    }

    /**
     * Get request method
     */
    public function method(): string
    {
        return request_method();
    }

    /**
     * Check if request method matches
     */
    public function isMethod(string $method): bool
    {
        return strtoupper($method) === $this->method();
    }

    /**
     * Get request path
     */
    public function path(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        return $uri;
    }

    /**
     * Get full URL
     */
    public function url(): string
    {
        $scheme = $this->isSecure() ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        return "$scheme://$host" . $this->path();
    }

    /**
     * Get full URL with query string
     */
    public function fullUrl(): string
    {
        $url = $this->url();
        $query = $this->server['QUERY_STRING'] ?? '';
        return $query ? "$url?$query" : $url;
    }

    /**
     * Check if request is over HTTPS
     */
    public function isSecure(): bool
    {
        return (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off')
            || ($this->server['SERVER_PORT'] ?? 80) == 443
            || ($this->header('x-forwarded-proto') === 'https');
    }

    /**
     * Check if request is AJAX
     */
    public function isAjax(): bool
    {
        return is_ajax();
    }

    /**
     * Check if request wants JSON response
     */
    public function wantsJson(): bool
    {
        return wants_json();
    }

    /**
     * Check if request is JSON content type
     */
    public function isJson(): bool
    {
        $contentType = $this->header('content-type', '');
        return str_contains($contentType, 'application/json');
    }

    /**
     * Get client IP address
     */
    public function ip(): string
    {
        return client_ip();
    }

    /**
     * Get user agent
     */
    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Get server variable
     */
    public function server(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }

    /**
     * Validate input
     */
    public function validate(array $rules): array
    {
        $validator = new Validator($this->all(), $rules);
        
        if (!$validator->validate()) {
            Session::flash('_errors', $validator->errors());
            Session::flash('_old_input', $this->all());
            back();
        }

        return $validator->validated();
    }

    /**
     * Validate or return errors for API
     */
    public function validateApi(array $rules): array
    {
        $validator = new Validator($this->all(), $rules);
        
        if (!$validator->validate()) {
            json(['errors' => $validator->errors()], 422);
        }

        return $validator->validated();
    }

    /**
     * Get authenticated user from session or API context
     */
    public function user(): ?array
    {
        // Check if user was authenticated by API middleware
        if (isset($GLOBALS['api_user'])) {
            return $GLOBALS['api_user'];
        }
        
        // Fall back to session user
        return Session::user();
    }
    }
