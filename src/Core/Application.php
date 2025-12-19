<?php
/**
 * Application Container & Bootstrap
 */

declare(strict_types=1);

namespace App\Core;

class Application
{
    private static ?self $instance = null;
    private array $bindings = [];
    private array $instances = [];
    private Router $router;

    private function __construct()
    {
        $this->registerCoreServices();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register core services
     */
    private function registerCoreServices(): void
    {
        // Database
        $this->singleton(Database::class, function () {
            return new Database();
        });

        // Session
        $this->singleton(Session::class, function () {
            return new Session();
        });

        // Router
        $this->singleton(Router::class, function () {
            return new Router();
        });

        // Auth
        $this->singleton('auth', function () {
            return new \App\Services\AuthService();
        });

        // Cache
        $this->singleton(Cache::class, function () {
            return new Cache();
        });

        // Logger
        $this->singleton(Logger::class, function () {
            return new Logger();
        });

        // Mailer
        $this->singleton(Mailer::class, function () {
            return new Mailer();
        });

        // Translator
        $this->singleton('translator', function () {
            return new Translator();
        });

        // View
        $this->singleton(View::class, function () {
            return new View();
        });
    }

    /**
     * Bind a class or interface to a concrete implementation
     */
    public function bind(string $abstract, callable|string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Bind a singleton
     */
    public function singleton(string $abstract, callable|string $concrete): void
    {
        $this->bind($abstract, $concrete);
        $this->instances[$abstract] = null; // Mark as singleton
    }

    /**
     * Resolve a binding from the container
     */
    public function resolve(string $abstract): mixed
    {
        // Return cached singleton
        if (array_key_exists($abstract, $this->instances) && $this->instances[$abstract] !== null) {
            return $this->instances[$abstract];
        }

        // Get concrete implementation
        $concrete = $this->bindings[$abstract] ?? $abstract;

        // Build the instance
        if (is_callable($concrete)) {
            $instance = $concrete($this);
        } elseif (is_string($concrete) && class_exists($concrete)) {
            $instance = $this->build($concrete);
        } else {
            throw new \RuntimeException("Unable to resolve: $abstract");
        }

        // Cache singleton
        if (array_key_exists($abstract, $this->instances)) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Build a class instance with dependency injection
     */
    private function build(string $class): object
    {
        $reflection = new \ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new \RuntimeException("Class $class is not instantiable");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();
            
            if ($type === null || $type->isBuiltin()) {
                if ($param->isDefaultValueAvailable()) {
                    $dependencies[] = $param->getDefaultValue();
                } else {
                    throw new \RuntimeException(
                        "Cannot resolve parameter {$param->getName()} in $class"
                    );
                }
            } else {
                $dependencies[] = $this->resolve($type->getName());
            }
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Get the router instance
     */
    public function getRouter(): Router
    {
        return $this->resolve(Router::class);
    }

    /**
     * Run the application
     */
    public function run(): void
    {
        try {
            // Start session
            $session = $this->resolve(Session::class);
            $session->start();

            // Generate CSRF token if not exists
            if (!Session::has('_csrf_token')) {
                Session::set('_csrf_token', bin2hex(random_bytes(32)));
            }

            // Load routes
            require_once BASE_PATH . '/routes/web.php';
            require_once BASE_PATH . '/routes/api.php';

            // Dispatch router
            $router = $this->getRouter();
            $router->dispatch();
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * Handle exceptions
     */
    private function handleException(\Throwable $e): void
    {
        // Log the error
        $logger = $this->resolve(Logger::class);
        $logger->error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        // Check if this is an API request
        $isApi = function_exists('is_api_request') && is_api_request();
        
        // Display error
        if ($isApi) {
            // Return JSON for API requests
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'error' => 'Internal Server Error',
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred',
                'status' => 500,
                'exception' => config('app.debug') ? get_class($e) : null,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } elseif (config('app.debug')) {
            echo '<div style="background:#ff5555;color:#fff;padding:20px;margin:20px;border-radius:5px;font-family:monospace;">';
            echo '<h2>' . get_class($e) . '</h2>';
            echo '<p><strong>Message:</strong> ' . e($e->getMessage()) . '</p>';
            echo '<p><strong>File:</strong> ' . e($e->getFile()) . ':' . $e->getLine() . '</p>';
            echo '<pre>' . e($e->getTraceAsString()) . '</pre>';
            echo '</div>';
        } else {
            abort(500);
        }
    }
}
