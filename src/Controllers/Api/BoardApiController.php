<?php
/**
 * Board API Controller
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Request;
use App\Services\BoardService;

class BoardApiController extends Controller
{
    private BoardService $boardService;

    public function __construct()
    {
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
        $filters = [
            'project_id' => $request->input('project_id'),
            'type' => $request->input('type'),
            'owner_id' => $request->input('owner_id'),
            'search' => $request->input('search'),
            'is_private' => $request->input('is_private') !== null 
                ? $request->input('is_private') === '1' 
                : null,
        ];

        $page = (int) ($request->input('page') ?? 1);
        $perPage = (int) ($request->input('per_page') ?? 25);

        $boards = $this->boardService->getAllBoards(
            array_filter($filters, fn($v) => $v !== null),
            $page,
            $perPage
        );

        $this->json($boards);
    }

    public function store(Request $request): never
    {
        $data = $request->validate([
            'project_id' => 'required|integer',
            'name' => 'required|max:255',
            'type' => 'nullable|in:scrum,kanban',
            'filter_jql' => 'nullable|max:5000',
            'is_private' => 'nullable|boolean',
        ]);

        try {
            $board = $this->boardService->createBoard(
                (int) $data['project_id'],
                $data,
                $this->apiUserId()
            );
            $this->json(['success' => true, 'board' => $board], 201);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(Request $request): never
    {
        $boardId = (int) $request->param('id');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            $this->json(['error' => 'Board not found'], 404);
        }

        $this->json($board);
    }

    public function update(Request $request): never
    {
        $boardId = (int) $request->param('id');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            $this->json(['error' => 'Board not found'], 404);
        }

        $data = $request->validate([
            'name' => 'nullable|max:255',
            'filter_jql' => 'nullable|max:5000',
            'is_private' => 'nullable|boolean',
        ]);

        try {
            $updated = $this->boardService->updateBoard($boardId, $data, $this->apiUserId());
            $this->json(['success' => true, 'board' => $updated]);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy(Request $request): never
    {
        $boardId = (int) $request->param('id');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            $this->json(['error' => 'Board not found'], 404);
        }

        try {
            $this->boardService->deleteBoard($boardId, $this->apiUserId());
            $this->json(['success' => true, 'message' => 'Board deleted successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public function issues(Request $request): never
    {
        $boardId = (int) $request->param('id');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            $this->json(['error' => 'Board not found'], 404);
        }

        $sprintId = $request->input('sprint_id') ? (int) $request->input('sprint_id') : null;
        $filters = [
            'assignee_id' => $request->input('assignee_id'),
            'issue_type_id' => $request->input('issue_type_id'),
            'search' => $request->input('search'),
        ];

        $data = $this->boardService->getBoardIssues(
            $boardId,
            $sprintId,
            array_filter($filters)
        );

        $this->json($data);
    }

    public function backlog(Request $request): never
    {
        $boardId = (int) $request->param('id');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            $this->json(['error' => 'Board not found'], 404);
        }

        $filters = [
            'search' => $request->input('search'),
            'issue_type_id' => $request->input('issue_type_id'),
            'priority_id' => $request->input('priority_id'),
            'assignee_id' => $request->input('assignee_id'),
            'epic_id' => $request->input('epic_id'),
        ];

        $page = (int) ($request->input('page') ?? 1);
        $perPage = (int) ($request->input('per_page') ?? 50);

        $backlog = $this->boardService->getBacklog(
            $boardId,
            array_filter($filters),
            $page,
            $perPage
        );

        $this->json($backlog);
    }

    public function rankIssues(Request $request): never
    {
        $boardId = (int) $request->param('id');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            $this->json(['error' => 'Board not found'], 404);
        }

        $data = $request->validate([
            'issue_ids' => 'required|array',
        ]);

        try {
            $this->boardService->rankIssues($data['issue_ids'], $this->apiUserId());
            $this->json(['success' => true, 'message' => 'Issues ranked successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }
}
