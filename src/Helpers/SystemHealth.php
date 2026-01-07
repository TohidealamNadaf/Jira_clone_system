<?php declare(strict_types=1);

namespace App\Helpers;

use App\Core\Database;

/**
 * System Health Helper
 * 
 * Provides real-time metrics for system monitoring
 */
class SystemHealth
{
    /**
     * Check Database Connectivity
     */
    public static function getDatabaseStatus(): array
    {
        try {
            // Simple query to verify connection
            Database::selectValue("SELECT 1");
            return [
                'status' => 'success',
                'label' => 'Connected',
                'message' => 'Database connection is healthy'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'danger',
                'label' => 'Disconnected',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Check Mailer Configuration and Status
     */
    public static function getMailerStatus(): array
    {
        $config = config('mail', []);
        $driver = $config['driver'] ?? 'mail';

        if ($driver === 'smtp') {
            $host = $config['host'] ?? '';
            $port = $config['port'] ?? 25;

            if (empty($host)) {
                return [
                    'status' => 'warning',
                    'label' => 'Misconfigured',
                    'message' => 'SMTP host is not defined'
                ];
            }

            // Attempt socket connection with short timeout
            $socket = @fsockopen($host, (int) $port, $errno, $errstr, 2);
            if ($socket) {
                fclose($socket);
                return [
                    'status' => 'success',
                    'label' => 'Operational',
                    'message' => "Connected to SMTP at $host"
                ];
            } else {
                return [
                    'status' => 'danger',
                    'label' => 'Unreachable',
                    'message' => "Could not connect to SMTP: $errstr ($errno)"
                ];
            }
        }

        return [
            'status' => 'success',
            'label' => 'Operational',
            'message' => 'Using PHP native mail()'
        ];
    }

    /**
     * Calculate Disk Space Usage in Storage Directory
     */
    public static function getDiskUsage(): array
    {
        $storagePath = storage_path('');

        try {
            $total = disk_total_space($storagePath);
            $free = disk_free_space($storagePath);
            $used = $total - $free;
            $percent = $total > 0 ? round(($used / $total) * 100) : 0;

            $status = 'success';
            if ($percent > 90)
                $status = 'danger';
            elseif ($percent > 75)
                $status = 'warning';

            return [
                'status' => $status,
                'percent' => $percent,
                'used' => self::formatBytes($used),
                'total' => self::formatBytes($total)
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'warning',
                'percent' => 0,
                'used' => 'Unknown',
                'total' => 'Unknown'
            ];
        }
    }

    /**
     * Format Bytes to Human Readable
     */
    private static function formatBytes(float $bytes, int $precision = 1): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Check Background Jobs (Email Queue)
     */
    public static function getQueueStatus(): array
    {
        try {
            $pending = (int) Database::selectValue("SELECT COUNT(*) FROM email_queue WHERE sent_at IS NULL AND failed_at IS NULL");
            $failed = (int) Database::selectValue("SELECT COUNT(*) FROM email_queue WHERE failed_at IS NOT NULL");

            $status = 'success';
            $label = 'Running';

            if ($failed > 10) {
                $status = 'warning';
                $label = 'Issues Detected';
            }

            return [
                'status' => $status,
                'label' => $label,
                'pending' => $pending,
                'failed' => $failed
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'gray',
                'label' => 'Disabled',
                'pending' => 0,
                'failed' => 0
            ];
        }
    }
}
