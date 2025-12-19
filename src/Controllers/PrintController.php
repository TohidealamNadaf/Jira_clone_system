<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\PrintService;
use App\Services\ProjectService;

/**
 * PrintController - Handles print and export functionality
 * Integrates with KoolReport for professional report generation
 */
class PrintController extends Controller
{
    private PrintService $printService;
    private ProjectService $projectService;
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->printService = new PrintService($database);
        $this->projectService = new ProjectService($database);
    }

    /**
     * Print board view
     * 
     * @param Request $request
     * @return void
     */
    public function printBoard(Request $request): void
    {
        $projectKey = $request->param('key');
        $sprintId = $request->query('sprint_id') ? (int)$request->query('sprint_id') : null;

        // Get project
        $project = $this->projectService->getByKey($projectKey);
        
        if (!$project) {
            http_response_code(404);
            echo 'Project not found';
            return;
        }

        // Check authorization
        if (!$this->authorize('projects.view', ['project_id' => $project['id']])) {
            http_response_code(403);
            echo 'Not authorized';
            return;
        }

        // Get data
        $data = $this->printService->getBoardDataForPrint($project['id'], $sprintId);

        // Generate HTML
        $html = $this->printService->generateHtmlReport('board', $data);

        // Set headers for print
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: inline; filename="board-' . $projectKey . '.html"');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        echo $html;
    }

    /**
     * Print project report
     * 
     * @param Request $request
     * @return void
     */
    public function printProject(Request $request): void
    {
        $projectKey = $request->param('key');

        // Get project
        $project = $this->projectService->getByKey($projectKey);
        
        if (!$project) {
            http_response_code(404);
            echo 'Project not found';
            return;
        }

        // Check authorization
        if (!$this->authorize('projects.view', ['project_id' => $project['id']])) {
            http_response_code(403);
            echo 'Not authorized';
            return;
        }

        // Get data
        $data = $this->printService->getProjectReportData($project['id']);

        // Generate HTML
        $html = $this->printService->generateHtmlReport('project', $data);

        // Set headers
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: inline; filename="project-' . $projectKey . '.html"');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        echo $html;
    }

    /**
     * Print sprint report
     * 
     * @param Request $request
     * @return void
     */
    public function printSprint(Request $request): void
    {
        $sprintId = (int)$request->param('sprint_id');

        // Get sprint
        $sprint = $this->database->selectOne(
            'SELECT s.*, p.key, p.name FROM sprints s
            JOIN projects p ON s.project_id = p.id
            WHERE s.id = ?',
            [$sprintId]
        );

        if (!$sprint) {
            http_response_code(404);
            echo 'Sprint not found';
            return;
        }

        // Check authorization
        if (!$this->authorize('projects.view', ['project_id' => $sprint['project_id']])) {
            http_response_code(403);
            echo 'Not authorized';
            return;
        }

        // Get data
        $data = $this->printService->getSprintReportData($sprintId);

        // Generate HTML
        $html = $this->printService->generateHtmlReport('sprint', $data);

        // Set headers
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: inline; filename="sprint-' . str_slug($data['sprint']['name']) . '.html"');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        echo $html;
    }

    /**
     * Export board to PDF (requires KoolReport)
     * 
     * @param Request $request
     * @return void
     */
    public function exportBoardPDF(Request $request): void
    {
        $projectKey = $request->param('key');

        // Get project
        $project = $this->projectService->getByKey($projectKey);
        
        if (!$project) {
            http_response_code(404);
            $this->json(['error' => 'Project not found'], 404);
            return;
        }

        // Check authorization
        if (!$this->authorize('projects.view', ['project_id' => $project['id']])) {
            http_response_code(403);
            $this->json(['error' => 'Not authorized'], 403);
            return;
        }

        try {
            // Get data
            $data = $this->printService->getBoardDataForPrint($project['id']);
            
            // Generate HTML
            $html = $this->printService->generateHtmlReport('board', $data);

            // If KoolReport is available, use it to generate PDF
            if (class_exists('\\KoolReport\\KoolReport')) {
                // Save HTML to temporary file
                $tempFile = tempnam(sys_get_temp_dir(), 'board_');
                file_put_contents($tempFile, $html);

                // Create PDF (simplified - actual implementation depends on your KoolReport setup)
                // For now, return HTML with print headers
                header('Content-Type: text/html; charset=utf-8');
                header('Content-Disposition: attachment; filename="board-' . $projectKey . '.pdf"');
                header('Cache-Control: no-cache, no-store, must-revalidate');

                echo $html;

                // Cleanup
                @unlink($tempFile);
            } else {
                // Fallback: return HTML for browser printing
                header('Content-Type: text/html; charset=utf-8');
                header('Content-Disposition: inline; filename="board-' . $projectKey . '.html"');

                echo $html;
            }
        } catch (\Exception $e) {
            http_response_code(500);
            $this->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export project report to PDF
     * 
     * @param Request $request
     * @return void
     */
    public function exportProjectPDF(Request $request): void
    {
        $projectKey = $request->param('key');

        // Get project
        $project = $this->projectService->getByKey($projectKey);
        
        if (!$project) {
            http_response_code(404);
            $this->json(['error' => 'Project not found'], 404);
            return;
        }

        // Check authorization
        if (!$this->authorize('projects.view', ['project_id' => $project['id']])) {
            http_response_code(403);
            $this->json(['error' => 'Not authorized'], 403);
            return;
        }

        try {
            // Get data
            $data = $this->printService->getProjectReportData($project['id']);
            
            // Generate HTML
            $html = $this->printService->generateHtmlReport('project', $data);

            // Return as printable HTML (PDF generation can be added later)
            header('Content-Type: text/html; charset=utf-8');
            header('Content-Disposition: inline; filename="project-' . $projectKey . '.html"');
            header('Cache-Control: no-cache, no-store, must-revalidate');

            echo $html;
        } catch (\Exception $e) {
            http_response_code(500);
            $this->json(['error' => 'Failed to generate report: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export sprint report to PDF
     * 
     * @param Request $request
     * @return void
     */
    public function exportSprintPDF(Request $request): void
    {
        $sprintId = (int)$request->param('sprint_id');

        // Get sprint
        $sprint = $this->database->selectOne(
            'SELECT s.*, p.key FROM sprints s
            JOIN projects p ON s.project_id = p.id
            WHERE s.id = ?',
            [$sprintId]
        );

        if (!$sprint) {
            http_response_code(404);
            $this->json(['error' => 'Sprint not found'], 404);
            return;
        }

        // Check authorization
        if (!$this->authorize('projects.view', ['project_id' => $sprint['project_id']])) {
            http_response_code(403);
            $this->json(['error' => 'Not authorized'], 403);
            return;
        }

        try {
            // Get data
            $data = $this->printService->getSprintReportData($sprintId);
            
            // Generate HTML
            $html = $this->printService->generateHtmlReport('sprint', $data);

            // Return as printable HTML
            header('Content-Type: text/html; charset=utf-8');
            header('Content-Disposition: inline; filename="sprint-' . str_slug($data['sprint']['name']) . '.html"');
            header('Cache-Control: no-cache, no-store, must-revalidate');

            echo $html;
        } catch (\Exception $e) {
            http_response_code(500);
            $this->json(['error' => 'Failed to generate report: ' . $e->getMessage()], 500);
        }
    }
}
