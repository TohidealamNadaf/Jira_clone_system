<?php
// Capture EVERYTHING
ob_start();

register_shutdown_function(function () {
    $output = ob_get_clean();
    $error = error_get_last();

    $log = "--- OUTPUT ---\n$output\n\n--- ERROR ---\n" . print_r($error, true);
    file_put_contents(__DIR__ . '/debug_output.log', $log);

    // Also echo to CLI for good measure
    echo "Check debug_output.log\n";
});

try {
    require_once __DIR__ . '/bootstrap/app.php';

    $issueService = new \App\Services\IssueService();
    $issueKey = 'ECOM-6';
    $targetStatusId = 2;
    $userId = 1;

    echo "Attempting transition for $issueKey...\n";

    // Manual transition logic to bypass controller
    $issue = $issueService->getIssueByKey($issueKey);
    if (!$issue)
        throw new Exception("Issue not found");

    $issueService->transitionIssue($issue['id'], $targetStatusId, $userId);
    echo "Transition success!\n";

} catch (\Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
