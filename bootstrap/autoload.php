<?php
/**
 * Custom PSR-4 Style Autoloader
 * No Composer required
 */

declare(strict_types=1);

class Autoloader
{
    private static array $prefixes = [];
    private static bool $registered = false;

    /**
     * Register the autoloader
     */
    public static function register(): void
    {
        if (self::$registered) {
            return;
        }

        spl_autoload_register([self::class, 'loadClass']);
        self::$registered = true;
    }

    /**
     * Add a namespace prefix and base directory
     */
    public static function addNamespace(string $prefix, string $baseDir): void
    {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!isset(self::$prefixes[$prefix])) {
            self::$prefixes[$prefix] = [];
        }

        self::$prefixes[$prefix][] = $baseDir;
    }

    /**
     * Load the class file for a given class name
     */
    public static function loadClass(string $class): bool
    {
        $prefix = $class;

        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);
            $relativeClass = substr($class, $pos + 1);

            if (self::loadMappedFile($prefix, $relativeClass)) {
                return true;
            }

            $prefix = rtrim($prefix, '\\');
        }

        return false;
    }

    /**
     * Load the mapped file for a namespace prefix and relative class
     */
    private static function loadMappedFile(string $prefix, string $relativeClass): bool
    {
        if (!isset(self::$prefixes[$prefix])) {
            return false;
        }

        foreach (self::$prefixes[$prefix] as $baseDir) {
            $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

            if (self::requireFile($file)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Require a file if it exists
     */
    private static function requireFile(string $file): bool
    {
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
}

// Register the autoloader
Autoloader::register();

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Register namespaces
Autoloader::addNamespace('App', BASE_PATH . '/src');
Autoloader::addNamespace('Tests', BASE_PATH . '/tests');
