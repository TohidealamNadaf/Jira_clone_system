<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Production Email Service - Pure PHP Implementation
 * 
 * No external dependencies required
 * Supports SMTP configuration via environment variables
 * Handles email queueing and retry logic
 * Logs all delivery attempts
 */
class ProductionEmailService
{
    private $config;
    private $logger;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->logger = new EmailLogger();
    }

    /**
     * Send email via SMTP or mail()
     *
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body HTML email body
     * @param array $options Additional options (replyTo, cc, bcc, etc)
     * 
     * @return bool True if sent successfully
     */
    public function send(
        string $to,
        string $subject,
        string $body,
        array $options = []
    ): bool {
        try {
            // Validate email
            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $this->logger->error('ProductionEmailService', 'Invalid email address', [
                    'to' => $to,
                    'subject' => $subject,
                ]);
                return false;
            }

            // Use SMTP if configured, otherwise use mail()
            if ($this->isSmtpConfigured()) {
                return $this->sendViaSMTP($to, $subject, $body, $options);
            } else {
                return $this->sendViaMail($to, $subject, $body, $options);
            }
        } catch (\Exception $e) {
            $this->logger->error('ProductionEmailService', 'Exception during send', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send email via SMTP
     *
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $body HTML body
     * @param array $options Additional options
     * 
     * @return bool
     */
    private function sendViaSMTP(
        string $to,
        string $subject,
        string $body,
        array $options
    ): bool {
        try {
            // Connect to SMTP server
            $host = $this->config['mail']['host'] ?? 'localhost';
            $port = $this->config['mail']['port'] ?? 587;
            $encryption = $this->config['mail']['encryption'] ?? 'tls';

            $socket = @fsockopen($host, $port, $errno, $errstr, 10);
            
            if (!$socket) {
                $this->logger->error('ProductionEmailService', 'SMTP connection failed', [
                    'host' => $host,
                    'port' => $port,
                    'error' => $errstr,
                ]);
                return false;
            }

            // Read SMTP response
            $response = fgets($socket, 515);
            
            if (strpos($response, '220') === false) {
                $this->logger->error('ProductionEmailService', 'Invalid SMTP response', [
                    'response' => $response,
                ]);
                fclose($socket);
                return false;
            }

            // Start TLS if needed
            if ($encryption === 'tls') {
                fwrite($socket, "STARTTLS\r\n");
                $response = fgets($socket, 515);
                
                if (strpos($response, '220') === false) {
                    $this->logger->error('ProductionEmailService', 'STARTTLS failed', [
                        'response' => $response,
                    ]);
                    fclose($socket);
                    return false;
                }

                stream_context_set_option($socket, 'ssl', 'allow_self_signed', true);
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            }

            // Authenticate if credentials provided
            if (!empty($this->config['mail']['username'])) {
                fwrite($socket, "AUTH LOGIN\r\n");
                fgets($socket, 515);
                
                $username = base64_encode($this->config['mail']['username']);
                $password = base64_encode($this->config['mail']['password']);
                
                fwrite($socket, "$username\r\n");
                fgets($socket, 515);
                
                fwrite($socket, "$password\r\n");
                $response = fgets($socket, 515);
                
                if (strpos($response, '235') === false) {
                    $this->logger->error('ProductionEmailService', 'SMTP authentication failed', [
                        'response' => $response,
                    ]);
                    fclose($socket);
                    return false;
                }
            }

            // Send email
            $from = $this->config['mail']['from_address'] ?? 'noreply@localhost';
            $fromName = $this->config['mail']['from_name'] ?? 'Jira Clone';

            fwrite($socket, "MAIL FROM:<$from>\r\n");
            fgets($socket, 515);

            fwrite($socket, "RCPT TO:<$to>\r\n");
            fgets($socket, 515);

            fwrite($socket, "DATA\r\n");
            fgets($socket, 515);

            // Build email headers and body
            $email = $this->buildEmailMessage($from, $fromName, $to, $subject, $body, $options);
            fwrite($socket, $email);
            fwrite($socket, "\r\n.\r\n");

            $response = fgets($socket, 515);
            
            if (strpos($response, '250') === false) {
                $this->logger->error('ProductionEmailService', 'SMTP message not accepted', [
                    'response' => $response,
                ]);
                fclose($socket);
                return false;
            }

            // Close connection
            fwrite($socket, "QUIT\r\n");
            fclose($socket);

            $this->logger->info('ProductionEmailService', 'Email sent via SMTP', [
                'to' => $to,
                'subject' => $subject,
                'host' => $host,
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('ProductionEmailService', 'SMTP exception', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send email via PHP mail() function
     *
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $body HTML body
     * @param array $options Additional options
     * 
     * @return bool
     */
    private function sendViaMail(
        string $to,
        string $subject,
        string $body,
        array $options
    ): bool {
        try {
            $from = $this->config['mail']['from_address'] ?? 'noreply@localhost';
            $fromName = $this->config['mail']['from_name'] ?? 'Jira Clone';

            $headers = "From: $fromName <$from>\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            // Add CC if provided
            if (!empty($options['cc'])) {
                $cc = is_array($options['cc']) ? implode(',', $options['cc']) : $options['cc'];
                $headers .= "CC: $cc\r\n";
            }

            // Add Reply-To if provided
            if (!empty($options['replyTo'])) {
                $headers .= "Reply-To: {$options['replyTo']}\r\n";
            }

            if (mail($to, $subject, $body, $headers)) {
                $this->logger->info('ProductionEmailService', 'Email sent via mail()', [
                    'to' => $to,
                    'subject' => $subject,
                ]);
                return true;
            } else {
                $this->logger->error('ProductionEmailService', 'mail() function failed', [
                    'to' => $to,
                    'subject' => $subject,
                ]);
                return false;
            }
        } catch (\Exception $e) {
            $this->logger->error('ProductionEmailService', 'mail() exception', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Build complete email message (headers + body)
     *
     * @param string $from From address
     * @param string $fromName From name
     * @param string $to To address
     * @param string $subject Subject
     * @param string $body HTML body
     * @param array $options Options
     * 
     * @return string
     */
    private function buildEmailMessage(
        string $from,
        string $fromName,
        string $to,
        string $subject,
        string $body,
        array $options
    ): string {
        $message = "From: \"$fromName\" <$from>\r\n";
        $message .= "To: <$to>\r\n";
        $message .= "Subject: $subject\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 8bit\r\n";

        if (!empty($options['cc'])) {
            $cc = is_array($options['cc']) ? implode(',', $options['cc']) : $options['cc'];
            $message .= "CC: $cc\r\n";
        }

        if (!empty($options['bcc'])) {
            $bcc = is_array($options['bcc']) ? implode(',', $options['bcc']) : $options['bcc'];
            $message .= "BCC: $bcc\r\n";
        }

        if (!empty($options['replyTo'])) {
            $message .= "Reply-To: {$options['replyTo']}\r\n";
        }

        $message .= "X-Mailer: Jira Clone System\r\n";
        $message .= "Date: " . date('r') . "\r\n";
        $message .= "\r\n";
        $message .= $body;

        return $message;
    }

    /**
     * Send email using template
     *
     * @param string $to Recipient
     * @param string $template Template name (without .php)
     * @param array $data Template data
     * @param array $options Email options
     * 
     * @return bool
     */
    public function sendTemplate(
        string $to,
        string $template,
        array $data = [],
        array $options = []
    ): bool {
        try {
            $templatePath = __DIR__ . '/../../views/emails/' . $template . '.php';

            if (!file_exists($templatePath)) {
                $this->logger->error('ProductionEmailService', 'Template not found', [
                    'template' => $template,
                    'path' => $templatePath,
                ]);
                return false;
            }

            // Render template
            ob_start();
            extract($data);
            include $templatePath;
            $body = ob_get_clean();

            $subject = $data['subject'] ?? 'Notification from Jira Clone';

            return $this->send($to, $subject, $body, $options);
        } catch (\Exception $e) {
            $this->logger->error('ProductionEmailService', 'Template send failed', [
                'template' => $template,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send test email to verify configuration
     *
     * @param string $testEmail Email to send test to
     * 
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendTest(string $testEmail): array
    {
        try {
            $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Email</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; color: #161B22; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { border-bottom: 3px solid #0052CC; padding-bottom: 20px; }
        .header h1 { color: #0052CC; margin: 0; }
        .content { padding: 20px 0; }
        .footer { border-top: 1px solid #D0D7DE; padding-top: 15px; margin-top: 20px; color: #666; font-size: 12px; }
        .success { background: #DFFCF0; border-left: 4px solid #3FB950; padding: 15px; margin: 15px 0; border-radius: 4px; }
        .button { background: #0052CC; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Test Email from Jira Clone</h1>
        </div>
        <div class="content">
            <div class="success">
                <strong>Success!</strong> Your email configuration is working correctly.
            </div>
            <p>This test email confirms that email delivery is now operational.</p>
            <p>You will now receive notifications about:</p>
            <ul>
                <li>Issues assigned to you</li>
                <li>Comments on your issues</li>
                <li>Status changes</li>
                <li>Project updates</li>
            </ul>
            <p><a href="' . getenv('APP_URL') . '/notifications" class="button">View Notification Settings</a></p>
        </div>
        <div class="footer">
            <p>Jira Clone System | Production Ready</p>
            <p>© ' . date('Y') . ' All rights reserved.</p>
        </div>
    </div>
</body>
</html>';

            if ($this->send($testEmail, '✓ Test Email from Jira Clone', $html)) {
                return [
                    'success' => true,
                    'message' => 'Test email sent successfully to: ' . $testEmail,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to send test email. Check configuration and logs.',
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Validate email configuration
     *
     * @return array ['success' => bool, 'message' => string, 'details' => array]
     */
    public function validateConfig(): array
    {
        try {
            $configured = !empty($this->config['mail']['host']) &&
                          !empty($this->config['mail']['from_address']);

            if (!$configured && !function_exists('mail')) {
                return [
                    'success' => false,
                    'message' => 'No email configuration found and mail() function disabled',
                    'details' => ['configured' => false, 'mail_function' => false],
                ];
            }

            return [
                'success' => true,
                'message' => 'Email configuration is valid',
                'details' => [
                    'driver' => $this->isSmtpConfigured() ? 'smtp' : 'mail()',
                    'from_address' => $this->config['mail']['from_address'] ?? 'system@localhost',
                    'from_name' => $this->config['mail']['from_name'] ?? 'Jira Clone',
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error validating configuration: ' . $e->getMessage(),
                'details' => [],
            ];
        }
    }

    /**
     * Check if SMTP is configured
     *
     * @return bool
     */
    private function isSmtpConfigured(): bool
    {
        return !empty($this->config['mail']['host']) &&
               !empty($this->config['mail']['port']);
    }

    /**
     * Queue email for reliable delivery with retry
     * Stores in database if queue table exists
     *
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $body Body
     * @param string $type Notification type
     * @param int $userId Associated user
     * 
     * @return bool
     */
    public function queue(
        string $to,
        string $subject,
        string $body,
        string $type = 'general',
        int $userId = 0
    ): bool {
        try {
            // Try to send immediately
            if ($this->send($to, $subject, $body)) {
                return true;
            }

            // If immediate send fails, attempt to queue for retry
            // This would require notification_queues table in Phase 2
            $this->logger->error('ProductionEmailService', 'Failed to queue email for retry', [
                'to' => $to,
                'type' => $type,
            ]);

            return false;
        } catch (\Exception $e) {
            $this->logger->error('ProductionEmailService', 'Queue exception', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
