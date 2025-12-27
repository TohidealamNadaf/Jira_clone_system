<?php
/**
 * Roadmap Controller - Manages roadmap views and API endpoints
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Database;
use App\Services\RoadmapService;
use App\Services\ProjectService;

class RoadmapController extends Controller
{
    private RoadmapService $roadmapService;
    private ProjectService $projectService;

    public function __construct()
    {
        $this->roadmapService = new RoadmapService();
        $this->projectService = new ProjectService();
    }

    /**
     * Show global roadmap with project selector
     */
    public function index(Request $request): string
    {
        $projectId = $request->input('project_id');
        $projects = $this->projectService->getAllProjects();
        
        // Default to first project if not specified
        if (empty($projectId) && !empty($projects['items'])) {
            $projectId = $projects['items'][0]['id'] ?? null;
        }

        $roadmapData = [];
        $selectedProject = null;

        if ($projectId) {
            $selectedProject = $this->projectService->getProjectById((int)$projectId);
            
            if ($selectedProject) {
                $this->authorize('issues.view', (int)$projectId);
                
                $filters = [
                    'status' => $request->input('status'),
                    'type' => $request->input('type'),
                    'owner_id' => $request->input('owner_id'),
                ];

                $roadmapData = [
                    'items' => $this->roadmapService->getProjectRoadmap((int)$projectId, array_filter($filters)),
                    'summary' => $this->roadmapService->getRoadmapSummary((int)$projectId),
                    'timeline' => $this->roadmapService->getTimelineRange((int)$projectId),
                    'atRiskItems' => $this->roadmapService->checkRiskStatus((int)$projectId),
                ];
            }
        }

        if ($request->wantsJson()) {
            $this->json([
                'projects' => $projects['items'] ?? [],
                'selected_project' => $selectedProject,
                'roadmap_data' => $roadmapData,
            ]);
        }

        return $this->view('roadmap.index', [
            'projects' => $projects['items'] ?? [],
            'selectedProject' => $selectedProject,
            'roadmapData' => $roadmapData,
        ]);
    }

    /**
     * Show project roadmap
     */
    public function show(Request $request): string
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('issues.view', $project['id']);

        // Get filter parameters
        $filters = [
            'status' => $request->input('status'),
            'type' => $request->input('type'),
            'owner_id' => $request->input('owner_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        // Get roadmap data
        $roadmapItems = $this->roadmapService->getProjectRoadmap($project['id'], array_filter($filters));
        $summary = $this->roadmapService->getRoadmapSummary($project['id']);
        $timeline = $this->roadmapService->getTimelineRange($project['id']);
        $atRiskItems = $this->roadmapService->checkRiskStatus($project['id']);

        // Get users for filter dropdowns
        $projectMembers = $this->projectService->getProjectMembers($project['id']);

        // Get all sprints for linking
        $sprints = Database::select(
            "SELECT s.id, s.name, s.status FROM sprints s 
             INNER JOIN boards b ON s.board_id = b.id 
             WHERE b.project_id = ? ORDER BY s.start_date DESC",
            [$project['id']]
        );

        // Get all issues for linking
        $issues = Database::select(
            "SELECT i.id, i.issue_key, i.summary FROM issues i 
             WHERE i.project_id = ? ORDER BY i.issue_key ASC",
            [$project['id']]
        );

        if ($request->wantsJson()) {
            $this->json([
                'project' => $project,
                'roadmap_items' => $roadmapItems,
                'summary' => $summary,
                'timeline' => $timeline,
                'at_risk_items' => $atRiskItems,
            ]);
        }

        return $this->view('projects.roadmap', [
            'project' => $project,
            'roadmapItems' => $roadmapItems,
            'summary' => $summary,
            'timeline' => $timeline,
            'atRiskItems' => $atRiskItems,
            'projectMembers' => $projectMembers,
            'sprints' => $sprints,
            'issues' => $issues,
            'filters' => $filters,
        ]);
    }

    /**
     * Store new roadmap item
     */
    public function store(Request $request): void
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('issues.create', $project['id']);

        // Use validateApi for AJAX/JSON requests, validate for form submissions
        if ($request->wantsJson() || $request->isJson()) {
            $data = $request->validateApi([
                'title' => 'required|max:255',
                'description' => 'nullable|max:5000',
                'type' => 'required|in:epic,feature,milestone',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'status' => 'required|in:planned,in_progress,on_track,at_risk,delayed,completed',
                'priority' => 'nullable|in:low,medium,high,critical',
                'progress' => 'nullable|integer|min:0|max:100',
                'owner_id' => 'nullable|integer',
                'color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
                'sprint_ids' => 'nullable|array',
                'issue_ids' => 'nullable|array',
            ]);
        } else {
            $data = $request->validate([
                'title' => 'required|max:255',
                'description' => 'nullable|max:5000',
                'type' => 'required|in:epic,feature,milestone',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'status' => 'required|in:planned,in_progress,on_track,at_risk,delayed,completed',
                'priority' => 'nullable|in:low,medium,high,critical',
                'progress' => 'nullable|integer|min:0|max:100',
                'owner_id' => 'nullable|integer',
                'color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
                'sprint_ids' => 'nullable|array',
                'issue_ids' => 'nullable|array',
            ]);
        }

        // Ensure progress is always set (default to 0)
        if (empty($data['progress'])) {
            $data['progress'] = 0;
        }

        try {
            $item = $this->roadmapService->createRoadmapItem(
                $project['id'],
                $data,
                $this->userId()
            );

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'item' => $item], 201);
            }

            $this->redirectWith(
                url("/projects/{$key}/roadmap"),
                'success',
                'Roadmap item created successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/projects/{$key}/roadmap"),
                'error',
                $e->getMessage()
            );
        }
    }

    /**
     * Update roadmap item
     */
    public function update(Request $request): void
    {
        $itemId = (int) $request->param('itemId');
        $item = $this->roadmapService->getRoadmapItem($itemId);

        if (!$item) {
            abort(404, 'Roadmap item not found');
        }

        $project = $this->projectService->getProjectById($item['project_id']);
        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('issues.edit', $project['id']);

        $data = $request->validate([
            'title' => 'nullable|max:255',
            'description' => 'nullable|max:5000',
            'type' => 'nullable|in:epic,feature,milestone',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|in:planned,in_progress,on_track,at_risk,delayed,completed',
            'priority' => 'nullable|in:low,medium,high,critical',
            'owner_id' => 'nullable|integer',
            'color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'sprint_ids' => 'nullable|array',
            'issue_ids' => 'nullable|array',
        ]);

        try {
            $updated = $this->roadmapService->updateRoadmapItem(
                $itemId,
                $data,
                $this->userId()
            );

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'item' => $updated]);
            }

            $this->redirectWith(
                url("/projects/{$project['key']}/roadmap"),
                'success',
                'Roadmap item updated successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/projects/{$project['key']}/roadmap"),
                'error',
                $e->getMessage()
            );
        }
    }

    /**
     * Delete roadmap item
     */
    public function destroy(Request $request): void
    {
        $itemId = (int) $request->param('itemId');
        $item = $this->roadmapService->getRoadmapItem($itemId);

        if (!$item) {
            abort(404, 'Roadmap item not found');
        }

        $project = $this->projectService->getProjectById($item['project_id']);
        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('issues.delete', $project['id']);

        try {
            $this->roadmapService->deleteRoadmapItem($itemId);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(
                url("/projects/{$project['key']}/roadmap"),
                'success',
                'Roadmap item deleted successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 500);
            }

            $this->redirectWith(
                url("/projects/{$project['key']}/roadmap"),
                'error',
                $e->getMessage()
            );
        }
    }

    /**
     * Get roadmap items as JSON (for API)
     */
    public function getRoadmapItems(Request $request): never
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            $this->json(['error' => 'Project not found'], 404);
        }

        $filters = [
            'status' => $request->input('status'),
            'type' => $request->input('type'),
            'owner_id' => $request->input('owner_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $items = $this->roadmapService->getProjectRoadmap($project['id'], array_filter($filters));

        $this->json([
            'success' => true,
            'items' => $items,
            'count' => count($items),
        ]);
    }

    /**
     * Get roadmap summary metrics
     */
    public function getSummary(Request $request): never
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            $this->json(['error' => 'Project not found'], 404);
        }

        $summary = $this->roadmapService->getRoadmapSummary($project['id']);
        $timeline = $this->roadmapService->getTimelineRange($project['id']);

        $this->json([
            'success' => true,
            'summary' => $summary,
            'timeline' => $timeline,
        ]);
    }

    /**
     * Get roadmap item detail
     */
    public function getItem(Request $request): never
    {
        $itemId = (int) $request->param('itemId');
        $item = $this->roadmapService->getRoadmapItem($itemId);

        if (!$item) {
            $this->json(['error' => 'Roadmap item not found'], 404);
        }

        $this->json([
            'success' => true,
            'item' => $item,
        ]);
    }

    /**
     * Check at-risk items
     */
    public function checkRisks(Request $request): never
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            $this->json(['error' => 'Project not found'], 404);
        }

        $atRiskItems = $this->roadmapService->checkRiskStatus($project['id']);

        $this->json([
            'success' => true,
            'at_risk_items' => $atRiskItems,
            'count' => count($atRiskItems),
        ]);
    }
}
