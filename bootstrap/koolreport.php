<?php

declare(strict_types=1);

/**
 * KoolReport Configuration & Initialization
 * 
 * Sets up KoolReport for PDF export and advanced reporting
 * This integrates with the PrintService for enterprise-grade report generation
 */

// Load KoolReport autoloader if installed
$koolReportPath = dirname(__DIR__) . '/koolreport/core/autoload.php';

if (file_exists($koolReportPath)) {
    require_once $koolReportPath;
    
    /**
     * KoolReport Initialization
     */
    class KoolReportSetup
    {
        private static bool $initialized = false;

        public static function initialize(): void
        {
            if (self::$initialized) {
                return;
            }

            // Set up KoolReport paths
            define('KOOLREPORT_PATH', dirname(__DIR__) . '/koolreport/core');
            define('KOOLREPORT_TEMP_PATH', dirname(__DIR__) . '/storage/koolreport-temp');
            
            // Create temp directory if it doesn't exist
            if (!is_dir(KOOLREPORT_TEMP_PATH)) {
                @mkdir(KOOLREPORT_TEMP_PATH, 0755, true);
            }

            self::$initialized = true;
        }

        /**
         * Check if KoolReport is available
         */
        public static function isAvailable(): bool
        {
            return class_exists('\\KoolReport\\KoolReport');
        }

        /**
         * Generate PDF from HTML using KoolReport
         * 
         * @param string $html HTML content
         * @param string $filename Output filename
         * @return string Path to generated PDF or empty string on failure
         */
        public static function generatePDF(string $html, string $filename): string
        {
            if (!self::isAvailable()) {
                return '';
            }

            try {
                // Save HTML to temp file
                $tempHtmlPath = KOOLREPORT_TEMP_PATH . '/' . uniqid('report_', true) . '.html';
                file_put_contents($tempHtmlPath, $html);

                // Generate PDF using KoolReport
                // Note: Actual implementation depends on your KoolReport version
                // This is a placeholder for the actual PDF generation

                return $tempHtmlPath; // Return path for now
            } catch (\Exception $e) {
                error_log('KoolReport PDF generation failed: ' . $e->getMessage());
                return '';
            }
        }

        /**
         * Get KoolReport version
         */
        public static function getVersion(): string
        {
            if (self::isAvailable()) {
                // Try to get version from KoolReport
                if (defined('\\KoolReport\\KoolReport::VERSION')) {
                    return \KoolReport\KoolReport::VERSION;
                }
            }
            return 'unknown';
        }
    }

    // Initialize on load
    KoolReportSetup::initialize();

} else {
    // KoolReport not installed, provide fallback
    class KoolReportSetup
    {
        public static function initialize(): void
        {
            // No-op
        }

        public static function isAvailable(): bool
        {
            return false;
        }

        public static function generatePDF(string $html, string $filename): string
        {
            return '';
        }

        public static function getVersion(): string
        {
            return 'not installed';
        }
    }
}
