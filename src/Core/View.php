<?php
/**
 * View/Template Engine
 */

declare(strict_types=1);

namespace App\Core;

class View
{
    private static array $shared = [];
    private static array $sections = [];
    private static ?string $currentSection = null;
    private static ?string $layout = null;
    private static string $content = '';

    /**
     * Share data with all views
     */
    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    /**
     * Render a view
     */
    public static function render(string $name, array $data = []): string
    {
        $path = self::getViewPath($name);

        if (!file_exists($path)) {
            throw new \RuntimeException("View not found: $name at path: $path");
        }

        // Reset state
        self::$sections = [];
        self::$layout = null;

        // Merge data with shared data
        $data = array_merge(self::$shared, $data);

        // Render view
        $content = self::renderFile($path, $data);

        // If layout was set, render it
        if (self::$layout !== null) {
            self::$content = $content;
            $layoutPath = self::getViewPath(self::$layout);
            if (!file_exists($layoutPath)) {
                throw new \RuntimeException("Layout not found: " . self::$layout . " at path: $layoutPath");
            }
            $content = self::renderFile($layoutPath, $data);
        }

        return $content;
    }

    /**
     * Render a view file
     */
    private static function renderFile(string $path, array $data): string
    {
        ob_start();
        extract($data);
        include $path;
        return ob_get_clean();
    }

    /**
     * Get full path to view file
     */
    private static function getViewPath(string $name): string
    {
        $name = str_replace('.', DIRECTORY_SEPARATOR, $name);
        return views_path($name . '.php');
    }

    /**
     * Extend a layout
     */
    public static function extends(string $layout): void
    {
        self::$layout = $layout;
    }

    /**
     * Start a section
     */
    public static function section(string $name): void
    {
        self::$currentSection = $name;
        ob_start();
    }

    /**
     * End a section
     */
    public static function endSection(): void
    {
        if (self::$currentSection === null) {
            throw new \RuntimeException('No section started');
        }

        self::$sections[self::$currentSection] = ob_get_clean();
        self::$currentSection = null;
    }

    /**
     * Yield a section
     */
    public static function yield(string $name, string $default = ''): string
    {
        return self::$sections[$name] ?? $default;
    }

    /**
     * Get the main content
     */
    public static function content(): string
    {
        return self::$content;
    }

    /**
     * Include a partial
     */
    public static function include(string $name, array $data = []): void
    {
        $path = self::getViewPath($name);

        if (!file_exists($path)) {
            throw new \RuntimeException("Partial not found: $name");
        }

        extract(array_merge(self::$shared, $data));
        include $path;
    }

    /**
     * Include a partial and return as string
     */
    public static function partial(string $name, array $data = []): string
    {
        ob_start();
        self::include($name, $data);
        return ob_get_clean();
    }

    /**
     * Render a component
     */
    public static function component(string $name, array $props = [], ?string $slot = null): void
    {
        $props['slot'] = $slot;
        self::include("components.$name", $props);
    }

    /**
     * Check if section exists
     */
    public static function hasSection(string $name): bool
    {
        return isset(self::$sections[$name]);
    }

    /**
     * Push to a stack
     */
    public static function push(string $name): void
    {
        self::section($name . '_stack_' . count(self::$sections));
    }

    /**
     * End push
     */
    public static function endPush(): void
    {
        self::endSection();
    }

    /**
     * Get stack content
     */
    public static function stack(string $name): string
    {
        $content = '';
        foreach (self::$sections as $key => $value) {
            if (str_starts_with($key, $name . '_stack_')) {
                $content .= $value;
            }
        }
        return $content;
    }
}
