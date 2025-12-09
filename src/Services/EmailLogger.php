<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Simple Logger for EmailService
 * 
 * Logs email operations to the notifications log file
 */
class EmailLogger
{
    private string $logFile;

    public function __construct()
    {
        $this->logFile = storage_path('logs/notifications.log');
    }

    /**
     * Log an info message
     *
     * @param string $source Source of the log (e.g., 'EmailService')
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    public function info(string $source, string $message, array $context = []): void
    {
        $this->log('INFO', $source, $message, $context);
    }

    /**
     * Log an error message
     *
     * @param string $source Source of the log (e.g., 'EmailService')
     * @param string $message Error message
     * @param array $context Additional context data
     * @return void
     */
    public function error(string $source, string $message, array $context = []): void
    {
        $this->log('ERROR', $source, $message, $context);
    }

    /**
     * Log a warning message
     *
     * @param string $source Source of the log
     * @param string $message Warning message
     * @param array $context Additional context data
     * @return void
     */
    public function warning(string $source, string $message, array $context = []): void
    {
        $this->log('WARNING', $source, $message, $context);
    }

    /**
     * Internal log method
     *
     * @param string $level Log level (INFO, ERROR, WARNING)
     * @param string $source Source of the log
     * @param string $message Log message
     * @param array $context Additional context
     * @return void
     */
    private function log(string $level, string $source, string $message, array $context): void
    {
        try {
            $timestamp = date('Y-m-d H:i:s');
            $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
            $logMessage = sprintf(
                "[%s] [%s] [%s] %s%s\n",
                $timestamp,
                $level,
                $source,
                $message,
                $contextStr
            );

            // Ensure the logs directory exists
            $logDir = dirname($this->logFile);
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }

            error_log($logMessage, 3, $this->logFile);
        } catch (\Exception $e) {
            // Fallback to PHP error log if file logging fails
            error_log("EmailLogger failed: " . $e->getMessage());
        }
    }
}
