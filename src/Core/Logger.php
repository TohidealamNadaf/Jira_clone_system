<?php
/**
 * Simple File Logger
 */

declare(strict_types=1);

namespace App\Core;

class Logger
{
    private string $path;
    private string $level;

    private const LEVELS = [
        'debug' => 0,
        'info' => 1,
        'warning' => 2,
        'error' => 3,
    ];

    public function __construct()
    {
        $this->path = config('logging.path', storage_path('logs'));
        $this->level = config('logging.level', 'debug');

        if (!is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
    }

    /**
     * Log debug message
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }

    /**
     * Log info message
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    /**
     * Log warning message
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    /**
     * Log error message
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    /**
     * Log message at specified level
     */
    public function log(string $level, string $message, array $context = []): void
    {
        // Check if level should be logged
        if (!$this->shouldLog($level)) {
            return;
        }

        // Format message
        $timestamp = date('Y-m-d H:i:s');
        $levelUpper = strtoupper($level);
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES) : '';
        $logMessage = "[$timestamp] $levelUpper: $message$contextStr" . PHP_EOL;

        // Write to file
        $filename = $this->path . '/' . date('Y-m-d') . '.log';
        file_put_contents($filename, $logMessage, FILE_APPEND | LOCK_EX);
    }

    /**
     * Check if level should be logged
     */
    private function shouldLog(string $level): bool
    {
        $levelValue = self::LEVELS[$level] ?? 0;
        $minLevel = self::LEVELS[$this->level] ?? 0;
        return $levelValue >= $minLevel;
    }

    /**
     * Log an exception
     */
    public function exception(\Throwable $e, array $context = []): void
    {
        $context = array_merge($context, [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        $this->error($e->getMessage(), $context);
    }

    /**
     * Clear old log files
     */
    public function cleanup(int $days = 30): int
    {
        $count = 0;
        $threshold = time() - ($days * 86400);

        $files = glob($this->path . '/*.log');
        foreach ($files as $file) {
            if (filemtime($file) < $threshold) {
                unlink($file);
                $count++;
            }
        }

        return $count;
    }
}
