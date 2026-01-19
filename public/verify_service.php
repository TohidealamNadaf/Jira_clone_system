<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
use App\Services\ProjectDocumentationService;
use App\Services\ProjectService;

try {
    $projectService = new ProjectService();
    $docService = new ProjectDocumentationService();

    $projectKey = 'CWAYSMIS';
    $project = $projectService->getProjectByKey($projectKey);

    if (!$project) {
        die("Project $projectKey not found\n");
    }

    echo "Testing getProjectDocuments for project ID: " . $project['id'] . "\n";
    $docs = $docService->getProjectDocuments($project['id']);
    echo "Success! Found " . count($docs) . " documents.\n";

    echo "\nTesting search filter...\n";
    $docsSearch = $docService->getProjectDocuments($project['id'], ['search' => 'test']);
    echo "Success! Search query executed without errors.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
