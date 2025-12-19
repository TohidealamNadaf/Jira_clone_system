<?php
/**
 * Roadmap Controller - Handles roadmap view and data requests
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\RoadmapService;

class RoadmapController extends Controller
{
    private RoadmapService $roadmapService;
    
    public function __construct()
    {
        $this->roadmapService = new RoadmapService();
    }
    
    /**
     * Display global roadmap view
     */
    public function index(Request $request): string
    {
        $this->authorize('projects.view');
        
        return $this->view('roadmap.index');
    }
    
    /**
     * Display roadmap view for a specific project
     */
    public function show(Request $request): string
    {
        $projectKey = $request->getParameter('key');
        
        $this->authorize('projects.view');
        
        return $this->view('projects.roadmap', [
            'projectKey' => $projectKey,
        ]);
    }
    
    /**
     * API endpoint: Get complete project roadmap (epics + versions + timeline)
     */
    public function project(Request $request): void
    {
        $this->authorize('projects.view');
        
        try {
            $projectKey = $request->input('project');
            
            if (!$projectKey) {
                $this->json(['success' => false, 'error' => 'Project key required'], 400);
                return;
            }
            
            $roadmap = $this->roadmapService->getProjectRoadmap($projectKey);
            $stats = $this->roadmapService->getProjectStats($projectKey);
            
            $this->json(['success' => true, 'data' => array_merge($roadmap, ['stats' => $stats])]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * API endpoint: Get epics for a project
     */
    public function epics(Request $request): void
    {
        $this->authorize('projects.view');
        
        try {
            $projectKey = $request->input('project');
            
            if (!$projectKey) {
                $this->json(['success' => false, 'error' => 'Project key required'], 400);
                return;
            }
            
            $epics = $this->roadmapService->getEpics($projectKey);
            
            $this->json(['success' => true, 'data' => $epics]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * API endpoint: Get versions for a project
     */
    public function versions(Request $request): void
    {
        $this->authorize('projects.view');
        
        try {
            $projectKey = $request->input('project');
            
            if (!$projectKey) {
                $this->json(['success' => false, 'error' => 'Project key required'], 400);
                return;
            }
            
            $versions = $this->roadmapService->getVersions($projectKey);
            
            $this->json(['success' => true, 'data' => $versions]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * API endpoint: Get all issues for a specific epic
     */
    public function epicIssues(Request $request): void
    {
        $this->authorize('projects.view');
        
        try {
            $epicId = (int) $request->input('epic_id');
            
            if (!$epicId) {
                $this->json(['success' => false, 'error' => 'Epic ID required'], 400);
                return;
            }
            
            $issues = $this->roadmapService->getEpicIssues($epicId);
            
            $this->json(['success' => true, 'data' => $issues]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * API endpoint: Get all issues for a specific version
     */
    public function versionIssues(Request $request): void
    {
        $this->authorize('projects.view');
        
        try {
            $versionId = (int) $request->input('version_id');
            
            if (!$versionId) {
                $this->json(['success' => false, 'error' => 'Version ID required'], 400);
                return;
            }
            
            $issues = $this->roadmapService->getVersionIssues($versionId);
            
            $this->json(['success' => true, 'data' => $issues]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * API endpoint: Get timeline range for roadmap visualization
     */
    public function timelineRange(Request $request): void
    {
        $this->authorize('projects.view');
        
        try {
            $projectKey = $request->input('project');
            
            if (!$projectKey) {
                $this->json(['success' => false, 'error' => 'Project key required'], 400);
                return;
            }
            
            $timeline = $this->roadmapService->getTimelineRange($projectKey);
            
            $this->json(['success' => true, 'data' => $timeline]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * API endpoint: Get all projects for selection dropdown
     */
    public function projects(Request $request): void
    {
        $this->authorize('projects.view');
        
        try {
            $projects = $this->roadmapService->getProjects();
            
            $this->json(['success' => true, 'data' => $projects]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
