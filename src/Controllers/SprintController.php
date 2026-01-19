<?php
/**
 * Sprint Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Services\SprintService;
use App\Services\BoardService;

class SprintController extends Controller
{
    private SprintService $sprintService;
    private BoardService $boardService;

    public function __construct()
    {
        $this->sprintService = new SprintService();
        $this->boardService = new BoardService();
    }

    public function index(Request $request): void
    {
        $boardId = (int) $request->param('boardId');
        $status = $request->input('status');

        $board = $this->boardService->getBoardById($boardId);
        if (!$board) {
            abort(404, 'Board not found');
        }

        $sprints = $this->sprintService->getSprints($boardId, $status);

        $this->json($sprints);
    }

    public function show(Request $request): void
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            abort(404, 'Sprint not found');
        }

        $this->json($sprint);
    }

    public function store(Request $request): void
    {
        $boardId = (int) $request->param('boardId');
        $board = $this->boardService->getBoardById($boardId);

        if (!$board) {
            abort(404, 'Board not found');
        }

        $this->authorize('sprints.create', $board['project_id']);

        $data = $request->validate([
            'name' => 'nullable|max:100',
            'goal' => 'nullable|max:2000',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        try {
            $sprint = $this->sprintService->createSprint($boardId, $data, $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'sprint' => $sprint], 201);
            }

            $this->redirectWith(
                url("/boards/{$boardId}/backlog"),
                'success',
                'Sprint created successfully.'
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            $this->redirect(url("/boards/{$boardId}/backlog"));
        }
    }

    public function update(Request $request): void
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            abort(404, 'Sprint not found');
        }

        $board = $this->boardService->getBoardById($sprint['board_id']);
        $this->authorize('sprints.edit', $board['project_id']);

        $data = $request->validate([
            'name' => 'nullable|max:100',
            'goal' => 'nullable|max:2000',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        try {
            $updated = $this->sprintService->updateSprint($sprintId, $data, $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'sprint' => $updated]);
            }

            $this->redirectWith(
                url("/boards/{$sprint['board_id']}/backlog"),
                'success',
                'Sprint updated successfully.'
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            $this->redirect(url("/boards/{$sprint['board_id']}/backlog"));
        }
    }

    public function destroy(Request $request): void
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            abort(404, 'Sprint not found');
        }

        $board = $this->boardService->getBoardById($sprint['board_id']);
        $this->authorize('sprints.delete', $board['project_id']);

        try {
            $boardId = $sprint['board_id'];
            $this->sprintService->deleteSprint($sprintId, $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(
                url("/boards/{$boardId}/backlog"),
                'success',
                'Sprint deleted successfully.'
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/boards/{$sprint['board_id']}/backlog"),
                'error',
                $e->getMessage()
            );
        }
    }

    public function start(Request $request): void
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            abort(404, 'Sprint not found');
        }

        $board = $this->boardService->getBoardById($sprint['board_id']);
        $this->authorize('sprints.manage', $board['project_id']);

        $data = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'goal' => 'nullable|max:2000',
        ]);

        try {
            $started = $this->sprintService->startSprint($sprintId, $data, $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'sprint' => $started]);
            }

            $this->redirectWith(
                url("/boards/{$sprint['board_id']}"),
                'success',
                "Sprint '{$started['name']}' has been started."
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/boards/{$sprint['board_id']}/backlog"),
                'error',
                $e->getMessage()
            );
        }
    }

    public function complete(Request $request): void
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            abort(404, 'Sprint not found');
        }

        $board = $this->boardService->getBoardById($sprint['board_id']);
        $this->authorize('sprints.manage', $board['project_id']);

        $data = $request->validate([
            'move_to_sprint_id' => 'nullable|integer',
        ]);

        try {
            $completed = $this->sprintService->completeSprint($sprintId, $data, $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'sprint' => $completed]);
            }

            $this->redirectWith(
                url("/boards/{$sprint['board_id']}/backlog"),
                'success',
                "Sprint '{$completed['name']}' has been completed. Velocity: {$completed['velocity']} points."
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/boards/{$sprint['board_id']}"),
                'error',
                $e->getMessage()
            );
        }
    }

    public function addIssue(Request $request): void
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            abort(404, 'Sprint not found');
        }

        $board = $this->boardService->getBoardById($sprint['board_id']);
        $this->authorize('sprints.manage', $board['project_id']);

        $data = $request->validate([
            'issue_id' => 'required|integer',
        ]);

        try {
            $this->sprintService->addIssueToSprint($sprintId, (int) $data['issue_id'], $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(
                url("/boards/{$sprint['board_id']}/backlog"),
                'success',
                'Issue added to sprint.'
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/boards/{$sprint['board_id']}/backlog"),
                'error',
                $e->getMessage()
            );
        }
    }

    public function removeIssue(Request $request): void
    {
        $sprintId = (int) $request->param('id');
        $issueId = (int) $request->param('issueId');

        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            abort(404, 'Sprint not found');
        }

        $board = $this->boardService->getBoardById($sprint['board_id']);
        $this->authorize('sprints.manage', $board['project_id']);

        try {
            $this->sprintService->removeIssueFromSprint($sprintId, $issueId, $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(
                url("/boards/{$sprint['board_id']}/backlog"),
                'success',
                'Issue removed from sprint.'
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/boards/{$sprint['board_id']}/backlog"),
                'error',
                $e->getMessage()
            );
        }
    }

    public function report(Request $request): string
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            abort(404, 'Sprint not found');
        }

        $report = $this->sprintService->getSprintReport($sprintId);
        $board = $this->boardService->getBoardById($sprint['board_id']);

        if ($request->wantsJson()) {
            $this->json($report);
        }

        return $this->view('sprints.report', [
            'sprint' => $sprint,
            'report' => $report,
            'board' => $board,
        ]);
    }

    public function burndown(Request $request): void
    {
        $sprintId = (int) $request->param('id');
        $sprint = $this->sprintService->getSprintById($sprintId);

        if (!$sprint) {
            abort(404, 'Sprint not found');
        }

        $burndown = $this->sprintService->calculateBurndown($sprintId);
        $this->json($burndown);
    }

    public function velocity(Request $request): void
    {
        $boardId = (int) $request->param('boardId');
        $sprintCount = (int) ($request->input('count') ?? 3);

        $board = $this->boardService->getBoardById($boardId);
        if (!$board) {
            abort(404, 'Board not found');
        }

        $velocity = $this->sprintService->getAverageVelocity($boardId, $sprintCount);
        $this->json(['average_velocity' => $velocity]);
    }

    /**
     * View a sprint's board
     * Route: GET /projects/{key}/sprints/{id}/board
     * Redirects to the board view for the sprint's board with sprint_id parameter
     */
    public function viewBoard(Request $request): void
    {
        $sprintId = (int) $request->param('id');
        $projectKey = $request->param('key');

        // Get sprint details including board_id
        $sprint = $this->sprintService->getSprintById($sprintId);
        if (!$sprint) {
            abort(404, 'Sprint not found');
        }

        // Verify the sprint belongs to the project
        if ($sprint['project_key'] !== $projectKey) {
            abort(404, 'Sprint not found in this project');
        }

        // Get the board for this sprint
        $board = $this->boardService->getBoardById($sprint['board_id']);
        if (!$board) {
            abort(404, 'Board not found');
        }

        // Redirect to board view with sprint_id parameter to filter to this sprint
        $this->redirect(url("/boards/{$board['id']}?sprint_id={$sprintId}"));
    }
}
