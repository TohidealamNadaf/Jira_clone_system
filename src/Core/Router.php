<?php
/**
 * HTTP Router
 */

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $namedRoutes = [];
    private array $groupStack = [];
    private array $middlewareAliases = [
        'auth' => \App\Middleware\AuthMiddleware::class,
        'guest' => \App\Middleware\GuestMiddleware::class,
        'csrf' => \App\Middleware\CsrfMiddleware::class,
        'admin' => \App\Middleware\AdminMiddleware::class,
        'api' => \App\Middleware\ApiMiddleware::class,
        'throttle' => \App\Middleware\ThrottleMiddleware::class,
    ];

    /**
     * Add GET route
     */
    public function get(string $path, array|string $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    /**
     * Add POST route
     */
    public function post(string $path, array|string $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    /**
     * Add PUT route
     */
    public function put(string $path, array|string $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    /**
     * Add PATCH route
     */
    public function patch(string $path, array|string $handler): self
    {
        return $this->addRoute('PATCH', $path, $handler);
    }

    /**
     * Add DELETE route
     */
    public function delete(string $path, array|string $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * Add route for multiple methods
     */
    public function match(array $methods, string $path, array|string $handler): self
    {
        foreach ($methods as $method) {
            $this->addRoute(strtoupper($method), $path, $handler);
        }
        return $this;
    }

    /**
     * Add route for any method
     */
    public function any(string $path, array|string $handler): self
    {
        return $this->match(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], $path, $handler);
    }

    /**
     * Add a route
     */
    private function addRoute(string $method, string $path, array|string $handler): self
    {
        $prefix = $this->getGroupPrefix();
        $middleware = $this->getGroupMiddleware();
        $fullPath = $prefix . '/' . ltrim($path, '/');
        $fullPath = rtrim($fullPath, '/') ?: '/';

        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'handler' => $handler,
            'middleware' => $middleware,
            'name' => null,
            'pattern' => $this->pathToPattern($fullPath),
        ];

        return $this;
    }

    /**
     * Name the last added route
     */
    public function name(string $name): self
    {
        $lastIndex = count($this->routes) - 1;
        if ($lastIndex >= 0) {
            $this->routes[$lastIndex]['name'] = $name;
            $this->namedRoutes[$name] = $lastIndex;
        }
        return $this;
    }

    /**
     * Add middleware to the last route
     */
    public function middleware(string|array $middleware): self
    {
        $lastIndex = count($this->routes) - 1;
        if ($lastIndex >= 0) {
            $middlewares = is_array($middleware) ? $middleware : [$middleware];
            $this->routes[$lastIndex]['middleware'] = array_merge(
                $this->routes[$lastIndex]['middleware'],
                $middlewares
            );
        }
        return $this;
    }

    /**
     * Create a route group
     */
    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    /**
     * Get current group prefix
     */
    private function getGroupPrefix(): string
    {
        $prefix = '';
        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
        }
        return $prefix;
    }

    /**
     * Get current group middleware
     */
    private function getGroupMiddleware(): array
    {
        $middleware = [];
        foreach ($this->groupStack as $group) {
            if (isset($group['middleware'])) {
                $middlewares = is_array($group['middleware']) ? $group['middleware'] : [$group['middleware']];
                $middleware = array_merge($middleware, $middlewares);
            }
        }
        return $middleware;
    }

    /**
     * Convert path to regex pattern
     */
    private function pathToPattern(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = preg_replace('/\{([a-zA-Z_]+):([^}]+)\}/', '(?P<$1>$2)', $pattern);
        return '#^' . $pattern . '$#';
    }

    /**
     * Generate URL for named route
     */
    public function url(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \RuntimeException("Route not found: $name");
        }

        $route = $this->routes[$this->namedRoutes[$name]];
        $path = $route['path'];

        foreach ($params as $key => $value) {
            $path = preg_replace('/\{' . $key . '(:([^}]+))?\}/', (string) $value, $path);
        }

        return url($path);
    }

    /**
     * Dispatch the current request
     */
    public function dispatch(): void
    {
        $method = request_method();
        $uri = $this->getUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->executeRoute($route, $params);
                return;
            }
        }

        // No route matched
        abort(404, 'Page not found');
    }

    /**
     * Get current URI
     */
    private function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove base path (Case Insensitive)
        $basePath = parse_url(config('app.url'), PHP_URL_PATH) ?? '';

        // Use case-insensitive check for Windows compatibility
        if ($basePath && stripos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        return '/' . trim($uri, '/') ?: '/';
    }

    /**
     * Execute a matched route
     */
    private function executeRoute(array $route, array $params): void
    {
        // Build middleware pipeline
        $middlewares = $this->resolveMiddleware($route['middleware']);

        // Create request
        $request = new Request($params);

        // Execute middleware pipeline
        $response = $this->runMiddleware($middlewares, $request, function ($request) use ($route, $params) {
            return $this->callHandler($route['handler'], $params);
        });

        // Send response
        if ($response !== null) {
            if (is_string($response)) {
                echo $response;
            } elseif (is_array($response)) {
                json($response);
            }
        }
    }

    /**
     * Resolve middleware aliases to classes
     */
    private function resolveMiddleware(array $middleware): array
    {
        return array_map(function ($m) {
            // Handle middleware with parameters like "throttle:60,1"
            $parts = explode(':', $m);
            $name = $parts[0];
            $params = isset($parts[1]) ? explode(',', $parts[1]) : [];

            $class = $this->middlewareAliases[$name] ?? $name;

            return ['class' => $class, 'params' => $params];
        }, $middleware);
    }

    /**
     * Run middleware pipeline
     */
    private function runMiddleware(array $middlewares, Request $request, callable $handler): mixed
    {
        if (empty($middlewares)) {
            return $handler($request);
        }

        $middleware = array_shift($middlewares);
        $class = $middleware['class'];
        $params = $middleware['params'];

        if (!class_exists($class)) {
            throw new \RuntimeException("Middleware not found: $class");
        }

        $instance = new $class();

        return $instance->handle($request, function ($request) use ($middlewares, $handler) {
            return $this->runMiddleware($middlewares, $request, $handler);
        }, ...$params);
    }

    /**
     * Call route handler
     */
    private function callHandler(array|string $handler, array $params): mixed
    {
        if (is_string($handler)) {
            // "Controller@method" format
            [$class, $method] = explode('@', $handler);
        } else {
            // [Controller::class, 'method'] format
            [$class, $method] = $handler;
        }

        if (!class_exists($class)) {
            throw new \RuntimeException("Controller not found: $class");
        }

        $controller = new $class();

        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Method not found: $class@$method");
        }

        return $controller->$method(new Request($params));
    }

    /**
     * Add resource routes (RESTful)
     */
    public function resource(string $name, string $controller): void
    {
        $singular = rtrim($name, 's');

        $this->get($name, [$controller, 'index'])->name("$name.index");
        $this->get("$name/create", [$controller, 'create'])->name("$name.create");
        $this->post($name, [$controller, 'store'])->name("$name.store");
        $this->get("$name/{{$singular}}", [$controller, 'show'])->name("$name.show");
        $this->get("$name/{{$singular}}/edit", [$controller, 'edit'])->name("$name.edit");
        $this->put("$name/{{$singular}}", [$controller, 'update'])->name("$name.update");
        $this->delete("$name/{{$singular}}", [$controller, 'destroy'])->name("$name.destroy");
    }

    /**
     * Add API resource routes (without create/edit)
     */
    public function apiResource(string $name, string $controller): void
    {
        $singular = rtrim($name, 's');

        $this->get($name, [$controller, 'index'])->name("api.$name.index");
        $this->post($name, [$controller, 'store'])->name("api.$name.store");
        $this->get("$name/{{$singular}}", [$controller, 'show'])->name("api.$name.show");
        $this->put("$name/{{$singular}}", [$controller, 'update'])->name("api.$name.update");
        $this->delete("$name/{{$singular}}", [$controller, 'destroy'])->name("api.$name.destroy");
    }
}
