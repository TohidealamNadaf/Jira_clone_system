<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Services\ProjectService;
use Exception;

/**
 * Project Budget API Controller
 * 
 * Handles budget management for projects
 */
class ProjectBudgetApiController extends Controller
{
    private ProjectService $projectService;

    public function __construct()
    {
        $this->projectService = new ProjectService();
    }

    /**
     * Get project budget status
     * 
     * GET /api/v1/projects/{projectId}/budget
     */
    public function getBudget(Request $request): void
    {
        try {
            $projectId = (int)$request->param('projectId');

            if (!$projectId) {
                $this->json(['error' => 'Project ID required'], 400);
                return;
            }

            $project = $this->projectService->getProjectById($projectId);
            if (!$project) {
                $this->json(['error' => 'Project not found'], 404);
                return;
            }

            $budgetStatus = $this->projectService->getBudgetStatus($projectId);

            $this->json([
                'success' => true,
                'budget' => $budgetStatus
            ]);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update project budget
     * 
     * PUT /api/v1/projects/{projectId}/budget
     * 
     * Request body:
     * {
     *     "budget": 10000.00,
     *     "currency": "USD"
     * }
     */
    public function updateBudget(Request $request): void
    {
        try {
            $user = Session::user();
            if (!$user) {
                $this->json(['error' => 'Unauthorized'], 401);
                return;
            }

            $projectId = (int)$request->param('projectId');
            if (!$projectId) {
                $this->json(['error' => 'Project ID required'], 400);
                return;
            }

            // Verify project exists
            $project = $this->projectService->getProjectById($projectId);
            if (!$project) {
                $this->json(['error' => 'Project not found'], 404);
                return;
            }

            // Get and validate JSON input directly (no exit calls)
            $json = $request->json();
            if (!$json) {
                $this->json(['error' => 'Invalid JSON request body'], 400);
                return;
            }

            $budget = $json['budget'] ?? null;
            $currency = $json['currency'] ?? 'USD';

            // Manual validation
            if ($budget === null) {
                $this->json(['error' => 'Budget amount is required'], 422);
                return;
            }

            $budget = floatval($budget);
            if ($budget < 0) {
                $this->json(['error' => 'Budget must be greater than or equal to 0'], 422);
                return;
            }

            if (empty($currency) || !is_string($currency)) {
                $this->json(['error' => 'Currency is required and must be a string'], 422);
                return;
            }

            if (strlen($currency) < 3 || strlen($currency) > 3) {
                $this->json(['error' => 'Currency must be a 3-letter code (e.g., USD, EUR)'], 422);
                return;
            }

            // Update budget
            $success = $this->projectService->setProjectBudget($projectId, $budget, strtoupper($currency));

            if ($success) {
                $budgetStatus = $this->projectService->getBudgetStatus($projectId);
                $this->json([
                    'success' => true,
                    'message' => 'Budget updated successfully',
                    'budget' => $budgetStatus
                ]);
            } else {
                $this->json(['error' => 'Failed to update budget'], 500);
            }
        } catch (Exception $e) {
            error_log('Budget update error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            $this->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}
