<?php
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Mailer;

$mailer = new Mailer();
$to = 'vostro631@gmail.com';
$subject = 'SMTP Test';
$body = '<h1>SMTP is working!</h1><p>Test sent at ' . date('Y-m-d H:i:s') . '</p>';

echo "Attempting to send test email to $to...\n";
$success = $mailer->send($to, $subject, $body);

if ($success) {
    echo "SUCCESS: Email sent successfully!\n";
} else {
    echo "FAILED: Email send failed. Check logs.\n";
}
