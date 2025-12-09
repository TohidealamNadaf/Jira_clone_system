<?php
/**
 * Email Sender (Native PHP + SMTP)
 */

declare(strict_types=1);

namespace App\Core;

class Mailer
{
    private string $driver;
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $encryption;
    private string $fromAddress;
    private string $fromName;

    public function __construct()
    {
        $config = config('mail', []);
        $this->driver = $config['driver'] ?? 'mail';
        $this->host = $config['host'] ?? 'localhost';
        $this->port = (int) ($config['port'] ?? 25);
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->encryption = $config['encryption'] ?? '';
        $this->fromAddress = $config['from_address'] ?? 'noreply@example.com';
        $this->fromName = $config['from_name'] ?? 'System';
    }

    /**
     * Send email
     */
    public function send(string $to, string $subject, string $body, array $options = []): bool
    {
        $toName = $options['to_name'] ?? '';
        $isHtml = $options['html'] ?? true;
        $from = $options['from'] ?? $this->fromAddress;
        $fromName = $options['from_name'] ?? $this->fromName;
        $replyTo = $options['reply_to'] ?? null;
        $cc = $options['cc'] ?? [];
        $bcc = $options['bcc'] ?? [];
        $attachments = $options['attachments'] ?? [];

        if ($this->driver === 'smtp') {
            return $this->sendSmtp($to, $toName, $subject, $body, $isHtml, $from, $fromName, $replyTo, $cc, $bcc, $attachments);
        }

        return $this->sendMail($to, $toName, $subject, $body, $isHtml, $from, $fromName, $replyTo, $cc, $bcc);
    }

    /**
     * Send using PHP mail()
     */
    private function sendMail(
        string $to,
        string $toName,
        string $subject,
        string $body,
        bool $isHtml,
        string $from,
        string $fromName,
        ?string $replyTo,
        array $cc,
        array $bcc
    ): bool {
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        
        if ($isHtml) {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
        } else {
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        }

        $headers[] = "From: $fromName <$from>";

        if ($replyTo) {
            $headers[] = "Reply-To: $replyTo";
        }

        if (!empty($cc)) {
            $headers[] = 'Cc: ' . implode(', ', $cc);
        }

        if (!empty($bcc)) {
            $headers[] = 'Bcc: ' . implode(', ', $bcc);
        }

        $recipient = $toName ? "$toName <$to>" : $to;

        return mail($recipient, $subject, $body, implode("\r\n", $headers));
    }

    /**
     * Send using SMTP
     */
    private function sendSmtp(
        string $to,
        string $toName,
        string $subject,
        string $body,
        bool $isHtml,
        string $from,
        string $fromName,
        ?string $replyTo,
        array $cc,
        array $bcc,
        array $attachments
    ): bool {
        try {
            // Connect to SMTP server
            $socket = $this->connectSmtp();

            // Authenticate
            $this->smtpCommand($socket, "EHLO " . gethostname());

            if ($this->encryption === 'tls') {
                $this->smtpCommand($socket, "STARTTLS");
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $this->smtpCommand($socket, "EHLO " . gethostname());
            }

            if ($this->username && $this->password) {
                $this->smtpCommand($socket, "AUTH LOGIN");
                $this->smtpCommand($socket, base64_encode($this->username));
                $this->smtpCommand($socket, base64_encode($this->password));
            }

            // Send email
            $this->smtpCommand($socket, "MAIL FROM:<$from>");
            $this->smtpCommand($socket, "RCPT TO:<$to>");

            foreach ($cc as $ccAddr) {
                $this->smtpCommand($socket, "RCPT TO:<$ccAddr>");
            }

            foreach ($bcc as $bccAddr) {
                $this->smtpCommand($socket, "RCPT TO:<$bccAddr>");
            }

            $this->smtpCommand($socket, "DATA");

            // Build email content
            $message = $this->buildEmailMessage(
                $to, $toName, $subject, $body, $isHtml,
                $from, $fromName, $replyTo, $cc, $attachments
            );

            fwrite($socket, $message . "\r\n.\r\n");
            $this->readSmtpResponse($socket);

            $this->smtpCommand($socket, "QUIT");
            fclose($socket);

            return true;
        } catch (\Exception $e) {
            app(Logger::class)->error('SMTP Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Connect to SMTP server
     */
    private function connectSmtp()
    {
        $protocol = $this->encryption === 'ssl' ? 'ssl://' : '';
        $socket = fsockopen($protocol . $this->host, $this->port, $errno, $errstr, 30);

        if (!$socket) {
            throw new \RuntimeException("SMTP connection failed: $errstr ($errno)");
        }

        $this->readSmtpResponse($socket);
        return $socket;
    }

    /**
     * Send SMTP command
     */
    private function smtpCommand($socket, string $command): string
    {
        fwrite($socket, $command . "\r\n");
        return $this->readSmtpResponse($socket);
    }

    /**
     * Read SMTP response
     */
    private function readSmtpResponse($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }

        $code = substr($response, 0, 3);
        if ($code[0] === '4' || $code[0] === '5') {
            throw new \RuntimeException("SMTP error: $response");
        }

        return $response;
    }

    /**
     * Build email message
     */
    private function buildEmailMessage(
        string $to,
        string $toName,
        string $subject,
        string $body,
        bool $isHtml,
        string $from,
        string $fromName,
        ?string $replyTo,
        array $cc,
        array $attachments
    ): string {
        $boundary = md5(time() . random_bytes(16));
        $recipient = $toName ? "\"$toName\" <$to>" : $to;

        $headers = [];
        $headers[] = "From: \"$fromName\" <$from>";
        $headers[] = "To: $recipient";
        $headers[] = "Subject: " . $this->encodeHeader($subject);
        $headers[] = "Date: " . date('r');
        $headers[] = "MIME-Version: 1.0";

        if ($replyTo) {
            $headers[] = "Reply-To: $replyTo";
        }

        if (!empty($cc)) {
            $headers[] = "Cc: " . implode(', ', $cc);
        }

        if (!empty($attachments)) {
            $headers[] = "Content-Type: multipart/mixed; boundary=\"$boundary\"";
        } elseif ($isHtml) {
            $headers[] = "Content-Type: text/html; charset=UTF-8";
        } else {
            $headers[] = "Content-Type: text/plain; charset=UTF-8";
        }

        $message = implode("\r\n", $headers) . "\r\n\r\n";

        if (!empty($attachments)) {
            $message .= "--$boundary\r\n";
            $contentType = $isHtml ? 'text/html' : 'text/plain';
            $message .= "Content-Type: $contentType; charset=UTF-8\r\n\r\n";
            $message .= $body . "\r\n";

            foreach ($attachments as $attachment) {
                $message .= "--$boundary\r\n";
                $message .= "Content-Type: {$attachment['mime']}; name=\"{$attachment['name']}\"\r\n";
                $message .= "Content-Transfer-Encoding: base64\r\n";
                $message .= "Content-Disposition: attachment; filename=\"{$attachment['name']}\"\r\n\r\n";
                $message .= chunk_split(base64_encode($attachment['data'])) . "\r\n";
            }

            $message .= "--$boundary--";
        } else {
            $message .= $body;
        }

        return $message;
    }

    /**
     * Encode header for non-ASCII characters
     */
    private function encodeHeader(string $text): string
    {
        if (preg_match('/[\x80-\xff]/', $text)) {
            return '=?UTF-8?B?' . base64_encode($text) . '?=';
        }
        return $text;
    }

    /**
     * Queue email for later sending
     */
    public function queue(string $to, string $subject, string $body, array $options = []): int
    {
        return Database::insert('email_queue', [
            'to_email' => $to,
            'to_name' => $options['to_name'] ?? '',
            'subject' => $subject,
            'body' => $body,
            'attempts' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Process email queue (called by cron)
     */
    public function processQueue(int $limit = 50): array
    {
        $emails = Database::select(
            "SELECT * FROM email_queue 
             WHERE sent_at IS NULL AND failed_at IS NULL AND attempts < 3 
             ORDER BY created_at ASC LIMIT ?",
            [$limit]
        );

        $results = ['sent' => 0, 'failed' => 0];

        foreach ($emails as $email) {
            $success = $this->send($email['to_email'], $email['subject'], $email['body'], [
                'to_name' => $email['to_name'],
            ]);

            if ($success) {
                Database::update('email_queue', [
                    'sent_at' => date('Y-m-d H:i:s'),
                    'attempts' => $email['attempts'] + 1,
                ], 'id = ?', [$email['id']]);
                $results['sent']++;
            } else {
                $attempts = $email['attempts'] + 1;
                $data = ['attempts' => $attempts];
                
                if ($attempts >= 3) {
                    $data['failed_at'] = date('Y-m-d H:i:s');
                    $data['error'] = 'Max attempts reached';
                }
                
                Database::update('email_queue', $data, 'id = ?', [$email['id']]);
                $results['failed']++;
            }
        }

        return $results;
    }
}
