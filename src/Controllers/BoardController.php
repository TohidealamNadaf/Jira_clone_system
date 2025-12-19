<?php
/**
 * Board Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Services\BoardService;
use App\Services\SprintService;
use App\Services\ProjectService;

class BoardController extends Controller
{
    private BoardService $boardService;
    private SprintService $sprintService;
    private ProjectService $projectService;

    public function __construct()
    {
        $this->boardService = new BoardService();
        $this->sprintService = new SprintService();
        $this->projectService = new ProjectService();
    }

    public function index(Request $request): string
    {
        $projectKey = $request->param('projectKey');
        $project = $this->projectService->getProjectByKey($projectKey);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $boards = $this->boardService->getBoards($project['id']);

        if ($request->wantsJson()) {
            $this->json($boards);
        }

        return $this->view('boards.index', [
            'project' => $project,
            'boards' => $boards,
        ]);
    }

    public function create(Request $request): string
    {
        $projectKey = $request->param('projectKey');
        $project = $this->projectService->getProjectByKey($projectKey);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('boards.create', $project['id']);

        return $this->view('boards.create', [
            'project' => $project,
        ]);
    }

    public function show(Request $request): string
    {
        $boardId = (int) $request->param('id');
        $sprintId = $request->input('sprint_id') ? (int) $request->input('sprint_id') : null;

        $board = $this->boardService->getBoardWithIssues($boardId, $sprintId);

        if (!$board) {
            abort(404, 'Board not found');
        }

        $project = $this->projectService->getProjectById($board['project_id']);
        $sprints = $this->sprintService->getSprints($boardId);

        if ($request->wantsJson()) {
            $this->json([
                'board' => $board,
                'sprints' => $sprints,
            ]);
        }

        return $this->view('boards.show', [
            'project' => $project,
            'board' => $board,
            'sprints' => $sprints,
        ]);
    }

    public function store(Request $request): void
    {
        $projectKey = $request->param('projectKey');
        $project = $this->projectService->getProjectByKey($projectKey);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('boards.create', $project['id']);

        $data = $request->validate([
            'name' => 'required|max:100',
            'type' => 'required|in:scrum,kanban',
            'filter_jql' => 'nullable|max:2000',
            'is_private' => 'nullable|boolean',
        ]);

        try {
            $board = $this->boardService->createBoard($project['id'], $data, $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'board' => $board], 201);
            }

            $this->redirectWith(
                url("/boards/{$board['id']}"),
                'success',
                'Board created successfully.'
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            $this->redirect(url("/projects/{$projectKey}/boards"));
        }
    }

    public function update(Request $request): void
    {
        $boardId = (int) $request->param('id');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            abort(404, 'Board not found');
        }

        $this->authorize('boards.edit', $board['project_id']);

        $data = $request->validate([
            'name' => 'nullable|max:100',
            'filter_jql' => 'nullable|max:2000',
            'is_private' => 'nullable|boolean',
        ]);

        try {
            $updated = $this->boardService->updateBoard($boardId, $data, $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'board' => $updated]);
            }

            $this->redirectWith(
                url("/boards/{$boardId}"),
                'success',
                'Board updated successfully.'
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            $this->redirect(url("/boards/{$boardId}"));
        }
    }

    public function destroy(Request $request): void
    {
        $boardId = (int) $request->param('id');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            abort(404, 'Board not found');
        }

        $this->authorize('boards.delete', $board['project_id']);

        try {
            $projectKey = $board['project_key'];
            $this->boardService->deleteBoard($boardId, $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(
                url("/projects/{$projectKey}/boards"),
                'success',
                'Board deleted successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 500);
            }

            $this->redirectWith(
                url("/boards/{$boardId}"),
                'error',
                'Failed to delete board.'
            );
        }
    }

    public function backlog(Request $request): string
    {
        $boardId = (int) $request->param('id');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            abort(404, 'Board not found');
        }

        $project = $this->projectService->getProjectById($board['project_id']);
        
        $page = (int) ($request->input('page') ?? 1);
        $backlogIssues = $this->boardService->getBacklogIssues($boardId, $page);
        
        $sprints = $this->sprintService->getSprints($boardId);
        $futureSprints = array_filter($sprints, fn($s) => $s['status'] !== 'completed');

        if ($request->wantsJson()) {
            $this->json([
                'backlog' => $backlogIssues,
                'sprints' => $futureSprints,
            ]);
        }

        return $this->view('boards.backlog', [
            'project' => $project,
            'board' => $board,
            'backlog' => $backlogIssues,
            'sprints' => $futureSprints,
        ]);
    }

    public function moveIssue(Request $request): void
    {
        $boardId = (int) $request->param('id');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            abort(404, 'Board not found');
        }

        $this->authorize('issues.transition', $board['project_id']);

        $data = $request->validate([
            'issue_id' => 'required|integer',
            'status_id' => 'required|integer',
            'sprint_id' => 'nullable|integer',
        ]);

        try {
            $issue = $this->boardService->moveIssue(
                (int) $data['issue_id'],
                (int) $data['status_id'],
                $data['sprint_id'] ? (int) $data['sprint_id'] : null,
                $this->userId()
            );

            $this->json(['success' => true, 'issue' => $issue]);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function rankIssue(Request $request): void
    {
        $boardId = (int) $request->param('id');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            abort(404, 'Board not found');
        }

        $this->authorize('issues.edit', $board['project_id']);

        $data = $request->validate([
            'issue_id' => 'required|integer',
            'after_issue_id' => 'required|integer',
        ]);

        try {
            $this->boardService->rankIssue(
                (int) $data['issue_id'],
                (int) $data['after_issue_id'],
                $this->userId()
            );

            $this->json(['success' => true]);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function settings(Request $request): string
    {
        $boardId = (int) $request->param('id');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            abort(404, 'Board not found');
        }

        $this->authorize('boards.admin', $board['project_id']);

        $project = $this->projectService->getProjectById($board['project_id']);

        return $this->view('boards.settings', [
            'project' => $project,
            'board' => $board,
        ]);
    }
}
