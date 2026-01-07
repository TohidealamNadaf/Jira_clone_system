<?php
$content = "[NOTIFICATION ERROR] Failed to dispatch comment notifications: issue=1, error=SQLSTATE[42000]...[NOTIFICATION RETRY] Queued for retry...[NOTIFICATION ERROR] Column not found: 1054 Unknown column 'key'\n[NOTIFICATION] Comment dispatch completed...";

// Improved logic
$errors_new = [];
// Split by tags using lookahead, keep the tags
$parts = preg_split('/(?=\[NOTIFICATION (?:ERROR|RETRY|SUCCESS|CREATED|STATUS|EMAIL|PUSH|WORKER))/', $content);
foreach ($parts as $part) {
    if (strpos($part, '[NOTIFICATION ERROR]') === 0) {
        // Split by newline or next tag to get just the error line/segment
        $msg = preg_split('/\r?\n|(?=\[NOTIFICATION)/', $part);
        $errors_new[] = trim($msg[0]);
    }
}

echo "Improved logic result:\n";
print_r($errors_new);
