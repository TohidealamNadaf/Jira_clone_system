<?php
/**
 * Sprint API Controller
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Request;
use App\Services\SprintService;
use App\Services\BoardService;

class SprintApiController extends Controller
{
    private SprintService $sprintService;
    private BoardService $boardService;

    public function __construct()
    {
        $this->sprintService = new SprintService();
        $this->boardService = new BoardService();
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
        $boardId = (int) $request->param('boardId');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            $this->json(['error' => 'Board not found'], 404);
        }

        $status = $request->input('status');
        $sprints = $this->sprintService->getSprintsByBoard($boardId, $status);

        $this->json($sprints);
    }

    public function store(Request $request): never
    {
        $boardId = (int) $request->param('boardId');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            $this->json(['error' => 'Board not found'], 404);
        }

        $data = $request->validate([
            'name' => 'nullable|max:255',
            'goal' => 'nullable|max:5000',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        try {
            $sprint = $this->sprintService->createSprint($boardId, $data, $this->apiUserId());
            $this->json(['success' => true, 'sprint' => $sprint], 201);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(Request $request): never
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            $this->json(['error' => 'Sprint not found'], 404);
        }

        $this->json($sprint);
    }

    public function update(Request $request): never
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            $this->json(['error' => 'Sprint not found'], 404);
        }

        $data = $request->validate([
            'name' => 'nullable|max:255',
            'goal' => 'nullable|max:5000',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        try {
            $updated = $this->sprintService->updateSprint($sprintId, $data, $this->apiUserId());
            $this->json(['success' => true, 'sprint' => $updated]);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy(Request $request): never
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            $this->json(['error' => 'Sprint not found'], 404);
        }

        try {
            $this->sprintService->deleteSprint($sprintId, $this->apiUserId());
            $this->json(['success' => true, 'message' => 'Sprint deleted successfully']);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public function start(Request $request): never
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            $this->json(['error' => 'Sprint not found'], 404);
        }

        $data = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'goal' => 'nullable|max:5000',
        ]);

        try {
            $started = $this->sprintService->startSprint($sprintId, $data, $this->apiUserId());
            $this->json(['success' => true, 'sprint' => $started]);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function complete(Request $request): never
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            $this->json(['error' => 'Sprint not found'], 404);
        }

        $data = $request->validate([
            'move_to_sprint_id' => 'nullable|integer',
        ]);

        try {
            $completed = $this->sprintService->completeSprint($sprintId, $data, $this->apiUserId());
            $this->json(['success' => true, 'sprint' => $completed]);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function issues(Request $request): never
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            $this->json(['error' => 'Sprint not found'], 404);
        }

        $issues = $this->sprintService->getSprintIssues($sprintId);
        $this->json($issues);
    }

    public function addIssue(Request $request): never
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            $this->json(['error' => 'Sprint not found'], 404);
        }

        $data = $request->validate([
            'issue_id' => 'required|integer',
        ]);

        try {
            $this->sprintService->addIssueToSprint($sprintId, (int) $data['issue_id'], $this->apiUserId());
            $this->json(['success' => true, 'message' => 'Issue added to sprint'], 201);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function removeIssue(Request $request): never
    {
        $sprintId = (int) $request->param('id');
        $issueId = (int) $request->param('issueId');

        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            $this->json(['error' => 'Sprint not found'], 404);
        }

        try {
            $this->sprintService->removeIssueFromSprint($sprintId, $issueId, $this->apiUserId());
            $this->json(['success' => true, 'message' => 'Issue removed from sprint']);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }
}
