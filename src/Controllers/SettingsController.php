<?php
/**
 * Settings Controller (Project Settings)
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Services\ProjectService;

class SettingsController extends Controller
{
    private ProjectService $projectService;

    public function __construct()
    {
        $this->projectService = new ProjectService();
    }

    public function components(Request $request): string
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('projects.admin', $project['id']);

        $components = Database::select(
            "SELECT c.*, 
                    u.display_name as lead_name,
                    da.display_name as default_assignee_name,
                    (SELECT COUNT(*) FROM issue_components ic WHERE ic.component_id = c.id) as issue_count
             FROM project_components c
             LEFT JOIN users u ON c.lead_id = u.id
             LEFT JOIN users da ON c.default_assignee_id = da.id
             WHERE c.project_id = ?
             ORDER BY c.name",
            [$project['id']]
        );

        if ($request->wantsJson()) {
            $this->json([
                'project' => $project,
                'components' => $components,
            ]);
        }

        return $this->view('settings.components', [
            'project' => $project,
            'components' => $components,
        ]);
    }

    public function versions(Request $request): string
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('projects.admin', $project['id']);

        $versions = Database::select(
            "SELECT v.*,
                    (SELECT COUNT(*) FROM issue_fix_versions ifv WHERE ifv.version_id = v.id) as issue_count,
                    (SELECT COUNT(*) FROM issue_fix_versions ifv 
                     JOIN issues i ON ifv.issue_id = i.id 
                     JOIN statuses s ON i.status_id = s.id
                     WHERE ifv.version_id = v.id AND s.category = 'done') as completed_count
             FROM project_versions v
             WHERE v.project_id = ?
             ORDER BY 
                CASE v.status 
                    WHEN 'unreleased' THEN 1 
                    WHEN 'released' THEN 2 
                    WHEN 'archived' THEN 3 
                END,
                v.release_date DESC NULLS LAST,
                v.name",
            [$project['id']]
        );

        foreach ($versions as &$version) {
            $version['progress'] = $version['issue_count'] > 0
                ? round(($version['completed_count'] / $version['issue_count']) * 100)
                : 0;
        }

        if ($request->wantsJson()) {
            $this->json([
                'project' => $project,
                'versions' => $versions,
            ]);
        }

        return $this->view('settings.versions', [
            'project' => $project,
            'versions' => $versions,
        ]);
    }

    public function labels(Request $request): string
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('projects.admin', $project['id']);

        $labels = Database::select(
            "SELECT l.*,
                    (SELECT COUNT(*) FROM issue_labels il WHERE il.label_id = l.id) as issue_count
             FROM labels l
             WHERE l.project_id = ? OR l.project_id IS NULL
             ORDER BY l.project_id IS NULL, l.name",
            [$project['id']]
        );

        $projectLabels = array_filter($labels, fn($l) => $l['project_id'] !== null);
        $globalLabels = array_filter($labels, fn($l) => $l['project_id'] === null);

        if ($request->wantsJson()) {
            $this->json([
                'project' => $project,
                'project_labels' => array_values($projectLabels),
                'global_labels' => array_values($globalLabels),
            ]);
        }

        return $this->view('settings.labels', [
            'project' => $project,
            'projectLabels' => array_values($projectLabels),
            'globalLabels' => array_values($globalLabels),
        ]);
    }
}
