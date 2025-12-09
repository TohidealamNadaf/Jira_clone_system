<?php
/**
 * File-based Cache Implementation
 */

declare(strict_types=1);

namespace App\Core;

class Cache
{
    private string $path;
    private string $prefix;
    private int $defaultTtl;

    public function __construct()
    {
        $this->path = config('cache.path', storage_path('cache'));
        $this->prefix = config('cache.prefix', 'cache_');
        $this->defaultTtl = config('cache.ttl', 3600);

        if (!is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
    }

    /**
     * Get cached value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return $default;
        }

        $content = file_get_contents($file);
        $data = unserialize($content);

        if ($data['expires'] !== 0 && $data['expires'] < time()) {
            $this->forget($key);
            return $default;
        }

        return $data['value'];
    }

    /**
     * Store value in cache
     */
    public function put(string $key, mixed $value, ?int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->defaultTtl;
        $expires = $ttl > 0 ? time() + $ttl : 0;

        $data = serialize([
            'value' => $value,
            'expires' => $expires,
        ]);

        $file = $this->getFilePath($key);
        return file_put_contents($file, $data, LOCK_EX) !== false;
    }

    /**
     * Store value forever
     */
    public function forever(string $key, mixed $value): bool
    {
        return $this->put($key, $value, 0);
    }

    /**
     * Check if key exists
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Remove value from cache
     */
    public function forget(string $key): bool
    {
        $file = $this->getFilePath($key);
        if (file_exists($file)) {
            return unlink($file);
        }
        return true;
    }

    /**
     * Clear all cache
     */
    public function flush(): bool
    {
        $files = glob($this->path . '/' . $this->prefix . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        return true;
    }

    /**
     * Get and delete
     */
    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->forget($key);
        return $value;
    }

    /**
     * Get or store value
     */
    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->put($key, $value, $ttl);

        return $value;
    }

    /**
     * Get or store forever
     */
    public function rememberForever(string $key, callable $callback): mixed
    {
        return $this->remember($key, 0, $callback);
    }

    /**
     * Increment value
     */
    public function increment(string $key, int $amount = 1): int
    {
        $value = (int) $this->get($key, 0);
        $value += $amount;
        $this->put($key, $value);
        return $value;
    }

    /**
     * Decrement value
     */
    public function decrement(string $key, int $amount = 1): int
    {
        return $this->increment($key, -$amount);
    }

    /**
     * Get multiple values
     */
    public function many(array $keys): array
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->get($key);
        }
        return $values;
    }

    /**
     * Store multiple values
     */
    public function putMany(array $values, ?int $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->put($key, $value, $ttl);
        }
        return true;
    }

    /**
     * Get file path for cache key
     */
    private function getFilePath(string $key): string
    {
        $hash = md5($key);
        return $this->path . '/' . $this->prefix . $hash;
    }

    /**
     * Clean expired cache files
     */
    public function gc(): int
    {
        $count = 0;
        $files = glob($this->path . '/' . $this->prefix . '*');

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $content = file_get_contents($file);
            $data = unserialize($content);

            if ($data['expires'] !== 0 && $data['expires'] < time()) {
                unlink($file);
                $count++;
            }
        }

        return $count;
    }
}
