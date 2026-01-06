<?php
/**
 * Internationalization (i18n) Translator
 */

declare(strict_types=1);

namespace App\Core;

class Translator
{
    private string $locale;
    private string $fallback;
    private array $translations = [];
    private string $path;

    public function __construct()
    {
        $this->locale = config('app.locale', 'en');
        $this->fallback = config('app.fallback_locale', 'en');
        $this->path = base_path('lang');

        $this->loadTranslations($this->locale);
        if ($this->locale !== $this->fallback) {
            $this->loadTranslations($this->fallback);
        }
    }

    /**
     * Load translations for a locale
     */
    private function loadTranslations(string $locale): void
    {
        $localePath = $this->path . '/' . $locale;

        if (!is_dir($localePath)) {
            return;
        }

        $files = glob($localePath . '/*.php');
        foreach ($files as $file) {
            $group = basename($file, '.php');
            $this->translations[$locale][$group] = require $file;
        }
    }

    /**
     * Get translation
     */
    public function get(string $key, array $replace = []): string
    {
        // Parse key (e.g., "messages.welcome" -> group: messages, key: welcome)
        $parts = explode('.', $key);
        $group = array_shift($parts);
        $subKey = implode('.', $parts);

        // Try current locale
        $translation = $this->findTranslation($this->locale, $group, $subKey);

        // Fallback to default locale
        if ($translation === null && $this->locale !== $this->fallback) {
            $translation = $this->findTranslation($this->fallback, $group, $subKey);
        }

        // Return key if not found
        if ($translation === null) {
            return $key;
        }

        // Replace placeholders
        return $this->replacePlaceholders($translation, $replace);
    }

    /**
     * Find translation in loaded translations
     */
    private function findTranslation(string $locale, string $group, string $key): ?string
    {
        if (!isset($this->translations[$locale][$group])) {
            return null;
        }

        $value = $this->translations[$locale][$group];
        
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return null;
            }
            $value = $value[$segment];
        }

        return is_string($value) ? $value : null;
    }

    /**
     * Replace placeholders in translation
     */
    private function replacePlaceholders(string $translation, array $replace): string
    {
        foreach ($replace as $key => $value) {
            $translation = str_replace(
                [':' . $key, ':' . strtoupper($key), ':' . ucfirst($key)],
                [$value, strtoupper((string) $value), ucfirst((string) $value)],
                $translation
            );
        }

        return $translation;
    }

    /**
     * Get current locale
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Set locale
     */
    public function setLocale(string $locale): void
    {
        if ($locale !== $this->locale) {
            $this->locale = $locale;
            $this->loadTranslations($locale);
        }
    }

    /**
     * Check if translation exists
     */
    public function has(string $key): bool
    {
        $parts = explode('.', $key);
        $group = array_shift($parts);
        $subKey = implode('.', $parts);

        return $this->findTranslation($this->locale, $group, $subKey) !== null
            || $this->findTranslation($this->fallback, $group, $subKey) !== null;
    }

    /**
     * Get translations for a group
     */
    public function group(string $group): array
    {
        return $this->translations[$this->locale][$group] 
            ?? $this->translations[$this->fallback][$group] 
            ?? [];
    }

    /**
     * Choice based on count
     */
    public function choice(string $key, int $count, array $replace = []): string
    {
        $translation = $this->get($key, $replace);
        
        // Parse choice format: "one item|:count items"
        $parts = explode('|', $translation);
        
        if (count($parts) === 1) {
            return $translation;
        }

        if (count($parts) === 2) {
            return $count === 1 ? $parts[0] : $parts[1];
        }

        // More complex: "{0} no items|{1} one item|[2,*] many items"
        foreach ($parts as $part) {
            if (preg_match('/^\{(\d+)\}\s*(.+)$/', $part, $matches)) {
                if ((int) $matches[1] === $count) {
                    return str_replace(':count', (string) $count, $matches[2]);
                }
            } elseif (preg_match('/^\[(\d+),(\d+|\*)\]\s*(.+)$/', $part, $matches)) {
                $min = (int) $matches[1];
                $max = $matches[2] === '*' ? PHP_INT_MAX : (int) $matches[2];
                if ($count >= $min && $count <= $max) {
                    return str_replace(':count', (string) $count, $matches[3]);
                }
            }
        }

        return str_replace(':count', (string) $count, end($parts));
    }
}
