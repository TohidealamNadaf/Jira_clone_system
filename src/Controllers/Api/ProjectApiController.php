<?php
/**
 * Project API Controller
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Database;
use App\Services\ProjectService;

class ProjectApiController extends Controller
{
    private ProjectService $projectService;

    public function __construct()
    {
        $this->projectService = new ProjectService();
    }

    private function apiUser(): array
    {
        return $GLOBALS['api_user'] ?? [];
    }

    private function apiUserId(): int
    {
        return (int) ($this->apiUser()['id'] ?? 0);
    }

    public function index(Request $request): never
    {
        $filters = [
            'search' => $request->input('search'),
            'is_archived' => $request->input('archived') === '1' ? true : ($request->input('archived') === '0' ? false : null),
            'category_id' => $request->input('category_id'),
            'lead_id' => $request->input('lead_id'),
        ];

        $page = (int) ($request->input('page') ?? 1);
        $perPage = (int) ($request->input('per_page') ?? 25);

        $projects = $this->projectService->getAllProjects(array_filter($filters), $page, $perPage);

        $this->json($projects);
    }

    public function store(Request $request): never
    {
        $data = $request->validate([
            'key' => 'required|alpha_num|min:2|max:10',
            'name' => 'required|max:255',
            'description' => 'nullable|max:5000',
            'lead_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'default_assignee' => 'nullable|in:project_lead,unassigned',
        ]);

        try {
            $project = $this->projectService->createProject($data, $this->apiUserId());
            $this->json(['success' => true, 'project' => $project], 201);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(Request $request): never
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            $this->json(['error' => 'Project not found'], 404);
        }

        $this->json($project);
    }

    public function update(Request $request): never
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            $this->json(['error' => 'Project not found'], 404);
        }

        $data = $request->validate([
            'name' => 'nullable|max:255',
            'key' => 'nullable|alpha_num|min:2|max:10',
            'description' => 'nullable|max:5000',
            'lead_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'default_assignee' => 'nullable|in:project_lead,unassigned',
            'is_archived' => 'nullable|boolean',
        ]);

        try {
            $updated = $this->projectService->updateProject($project['id'], $data, $this->apiUserId());
            $this->json(['success' => true, 'project' => $updated]);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy(Request $request): never
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            $this->json(['error' => 'Project not found'], 404);
        }

        try {
            $this->projectService->deleteProject($project['id'], $this->apiUserId());
            $this->json(['success' => true, 'message' => 'Project deleted successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public function members(Request $request): never
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            $this->json(['error' => 'Project not found'], 404);
        }

        $members = $this->projectService->getProjectMembers($project['id']);
        $this->json($members);
    }

    public function addMember(Request $request): never
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            $this->json(['error' => 'Project not found'], 404);
        }

        $data = $request->validate([
            'user_id' => 'required|integer',
            'role_id' => 'required|integer',
        ]);

        try {
            $this->projectService->addProjectMember(
                $project['id'],
                (int) $data['user_id'],
                (int) $data['role_id'],
                $this->apiUserId()
            );
            $this->json(['success' => true, 'message' => 'Member added successfully'], 201);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function removeMember(Request $request): never
    {
        $key = $request->param('key');
        $userId = (int) $request->param('userId');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            $this->json(['error' => 'Project not found'], 404);
        }

        try {
            $this->projectService->removeProjectMember($project['id'], $userId, $this->apiUserId());
            $this->json(['success' => true, 'message' => 'Member removed successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function components(Request $request): never
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            $this->json(['error' => 'Project not found'], 404);
        }

        $components = $this->projectService->getComponents($project['id']);
        $this->json($components);
    }

    public function storeComponent(Request $request): never
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            $this->json(['error' => 'Project not found'], 404);
        }

        $data = $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:1000',
            'lead_id' => 'nullable|integer',
            'default_assignee_id' => 'nullable|integer',
        ]);

        try {
            $component = $this->projectService->createComponent($project['id'], $data, $this->apiUserId());
            $this->json(['success' => true, 'component' => $component], 201);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function updateComponent(Request $request): never
    {
        $componentId = (int) $request->param('id');

        $component = Database::selectOne("SELECT * FROM project_components WHERE id = ?", [$componentId]);
        if (!$component) {
            $this->json(['error' => 'Component not found'], 404);
        }

        $data = $request->validate([
            'name' => 'nullable|max:100',
            'description' => 'nullable|max:1000',
            'lead_id' => 'nullable|integer',
            'default_assignee_id' => 'nullable|integer',
        ]);

        try {
            $updated = $this->projectService->updateComponent($componentId, $data, $this->apiUserId());
            $this->json(['success' => true, 'component' => $updated]);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroyComponent(Request $request): never
    {
        $componentId = (int) $request->param('id');

        $component = Database::selectOne("SELECT * FROM project_components WHERE id = ?", [$componentId]);
        if (!$component) {
            $this->json(['error' => 'Component not found'], 404);
        }

        try {
            $this->projectService->deleteComponent($componentId, $this->apiUserId());
            $this->json(['success' => true, 'message' => 'Component deleted successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public function versions(Request $request): never
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            $this->json(['error' => 'Project not found'], 404);
        }

        $versions = $this->projectService->getVersions($project['id']);
        $this->json($versions);
    }

    public function storeVersion(Request $request): never
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            $this->json(['error' => 'Project not found'], 404);
        }

        $data = $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:1000',
            'start_date' => 'nullable|date',
            'release_date' => 'nullable|date',
        ]);

        try {
            $version = $this->projectService->createVersion($project['id'], $data, $this->apiUserId());
            $this->json(['success' => true, 'version' => $version], 201);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function updateVersion(Request $request): never
    {
        $versionId = (int) $request->param('id');

        $version = Database::selectOne("SELECT * FROM project_versions WHERE id = ?", [$versionId]);
        if (!$version) {
            $this->json(['error' => 'Version not found'], 404);
        }

        $data = $request->validate([
            'name' => 'nullable|max:100',
            'description' => 'nullable|max:1000',
            'start_date' => 'nullable|date',
            'release_date' => 'nullable|date',
            'is_released' => 'nullable|boolean',
            'is_archived' => 'nullable|boolean',
        ]);

        try {
            $updated = $this->projectService->updateVersion($versionId, $data, $this->apiUserId());
            $this->json(['success' => true, 'version' => $updated]);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroyVersion(Request $request): never
    {
        $versionId = (int) $request->param('id');

        $version = Database::selectOne("SELECT * FROM project_versions WHERE id = ?", [$versionId]);
        if (!$version) {
            $this->json(['error' => 'Version not found'], 404);
        }

        try {
            $this->projectService->deleteVersion($versionId, $this->apiUserId());
            $this->json(['success' => true, 'message' => 'Version deleted successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
