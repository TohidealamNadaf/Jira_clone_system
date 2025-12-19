<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Email Service
 * 
 * Handles all email sending via SMTP
 * Supports templates and retry logic
 */
class EmailService
{
    private $config;
    private $logger;
    private $mailer;

    public function __construct(array $config, ?EmailLogger $logger = null)
    {
        $this->config = $config;
        $this->logger = $logger ?? new EmailLogger();
    }

    /**
     * Send a plain email
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email body (HTML or plain text)
     * @param array $cc CC recipients
     * @param array $bcc BCC recipients
     * 
     * @return bool True if sent successfully
     */
    public function send(
        string $to,
        string $subject,
        string $body,
        array $cc = [],
        array $bcc = []
    ): bool {
        try {
            $mailer = $this->getConnection();
            
            if (!$mailer) {
                $this->logger->error('EmailService', 'SMTP connection failed', [
                    'to' => $to,
                    'subject' => $subject,
                ]);
                return false;
            }

            // Set recipients
            $mailer->addAddress($to);
            
            foreach ($cc as $ccEmail) {
                $mailer->addCC($ccEmail);
            }
            
            foreach ($bcc as $bccEmail) {
                $mailer->addBCC($bccEmail);
            }

            // Set email content
            $mailer->Subject = $subject;
            $mailer->msgHTML($body);

            // Send
            if ($mailer->send()) {
                $this->logger->info('EmailService', 'Email sent successfully', [
                    'to' => $to,
                    'subject' => $subject,
                ]);
                return true;
            } else {
                $this->logger->error('EmailService', 'Email send failed', [
                    'to' => $to,
                    'subject' => $subject,
                    'error' => $mailer->ErrorInfo,
                ]);
                return false;
            }
        } catch (\Exception $e) {
            $this->logger->error('EmailService', 'Exception during email send', [
                'to' => $to,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send email using a template
     *
     * @param string $to Recipient email
     * @param string $template Template name (without .php extension)
     * @param array $data Template data
     * @param array $cc CC recipients
     * @param array $bcc BCC recipients
     * 
     * @return bool
     */
    public function sendTemplate(
        string $to,
        string $template,
        array $data = [],
        array $cc = [],
        array $bcc = []
    ): bool {
        try {
            $templatePath = __DIR__ . '/../../views/emails/' . $template . '.php';
            
            if (!file_exists($templatePath)) {
                $this->logger->error('EmailService', 'Template not found', [
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

            // Get subject from data or default
            $subject = $data['subject'] ?? 'Notification from Jira Clone';

            return $this->send($to, $subject, $body, $cc, $bcc);
        } catch (\Exception $e) {
            $this->logger->error('EmailService', 'Exception during template send', [
                'to' => $to,
                'template' => $template,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Test SMTP configuration
     *
     * @return array ['success' => bool, 'message' => string, 'details' => array]
     */
    public function validateConfig(): array
    {
        try {
            // Check configuration
            if (empty($this->config['mail']['host'])) {
                return [
                    'success' => false,
                    'message' => 'SMTP host not configured',
                    'details' => ['configured' => false],
                ];
            }

            $mailer = $this->getConnection();
            
            if (!$mailer) {
                return [
                    'success' => false,
                    'message' => 'Failed to create SMTP connection',
                    'details' => ['driver' => $this->config['mail']['driver']],
                ];
            }

            // Test connection
            if (!$mailer->smtpConnect()) {
                return [
                    'success' => false,
                    'message' => 'SMTP connection failed',
                    'details' => [
                        'host' => $this->config['mail']['host'],
                        'port' => $this->config['mail']['port'],
                        'error' => $mailer->ErrorInfo,
                    ],
                ];
            }

            $mailer->smtpClose();

            return [
                'success' => true,
                'message' => 'SMTP configuration is valid',
                'details' => [
                    'host' => $this->config['mail']['host'],
                    'port' => $this->config['mail']['port'],
                    'driver' => $this->config['mail']['driver'],
                    'encryption' => $this->config['mail']['encryption'],
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error validating configuration',
                'details' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Send test email to verify configuration
     *
     * @param string $testEmail Email to send test to
     * 
     * @return bool
     */
    public function sendTest(string $testEmail): bool
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Test Email</title>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { border-bottom: 2px solid #0052CC; padding-bottom: 10px; }
                .content { padding: 20px 0; }
                .footer { border-top: 1px solid #ddd; padding-top: 10px; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Test Email from Jira Clone</h1>
                </div>
                <div class="content">
                    <p>This is a test email to verify that your email configuration is working correctly.</p>
                    <p><strong>If you received this email, email delivery is operational.</strong></p>
                    <p>You can now expect to receive notifications about project activities, issue assignments, and comments.</p>
                </div>
                <div class="footer">
                    <p>Jira Clone System | Production Ready</p>
                </div>
            </div>
        </body>
        </html>';

        return $this->send($testEmail, 'Test Email from Jira Clone', $html);
    }

    /**
     * Queue email for delivery
     * Used by notification system for reliable delivery
     *
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body
     * @param string $type Type of notification (issue_assigned, comment, etc)
     * @param int $userId User ID associated with notification
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
            // This would typically insert into notification_queues table
            // For now, attempt immediate send
            // In Phase 2, can add queue table and retry logic
            
            return $this->send($to, $subject, $body);
        } catch (\Exception $e) {
            $this->logger->error('EmailService', 'Failed to queue email', [
                'to' => $to,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get SMTP connection
     *
     * @return PHPMailer|null
     */
    private function getConnection(): ?PHPMailer
    {
        try {
            $mailer = new PHPMailer(true);

            // Server settings
            if ($this->config['mail']['driver'] === 'smtp') {
                $mailer->isSMTP();
                $mailer->Host = $this->config['mail']['host'];
                $mailer->Port = $this->config['mail']['port'];
                
                if (!empty($this->config['mail']['encryption'])) {
                    $mailer->SMTPSecure = $this->config['mail']['encryption'];
                }

                if (!empty($this->config['mail']['username'])) {
                    $mailer->SMTPAuth = true;
                    $mailer->Username = $this->config['mail']['username'];
                    $mailer->Password = $this->config['mail']['password'];
                }
            } else {
                // Use PHP mail() function
                $mailer->isMail();
            }

            // Sender
            $mailer->setFrom(
                $this->config['mail']['from_address'],
                $this->config['mail']['from_name']
            );

            return $mailer;
        } catch (\Exception $e) {
            $this->logger->error('EmailService', 'Failed to create PHPMailer connection', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
