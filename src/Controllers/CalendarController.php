<?php
/**
 * Calendar Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\CalendarService;

class CalendarController extends Controller
{
    private CalendarService $calendarService;

    public function __construct()
    {
        $this->calendarService = new CalendarService();
    }

    /**
     * Display Calendar View
     */
    public function index(): string
    {
        if (!$this->authenticated()) {
            $this->redirect('/login');
        }
        return $this->view('calendar.index');
    }

    /**
     * Display Project Calendar Routes
     */
    public function show(Request $request): string
    {
        if (!$this->authenticated()) {
            $this->redirect('/login');
        }
        // We can reuse the main view but pass the project key to pre-filter in JS if needed
        return $this->view('calendar.index', ['projectKey' => $request->param('key')]);
    }

    /**
     * API: Get Events
     */
    public function getEvents(Request $request): void
    {
        if (!$this->authenticated()) {
            $this->redirect('/login');
        }

        try {
            // FullCalendar sends 'start' and 'end' as ISO strings
            $startRaw = $request->input('start');
            $endRaw = $request->input('end');

            // Format dates for MySQL (ISO8601 -> Y-m-d H:i:s)
            $start = $startRaw ? date('Y-m-d H:i:s', strtotime($startRaw)) : null;
            $end = $endRaw ? date('Y-m-d H:i:s', strtotime($endRaw)) : null;

            // Filters
            $projectKey = $request->input('project'); // Key or ID? Service expects key if we use getProjectDateRangeEvents? 
            // Actually getEvents in Service takes 'project_key' in filters array.

            // Currently CalendarService::getEvents logic:
            // if filters['project_key'] is set, it adds WHERE clause.

            $filters = [];
            if ($projectKey) {
                $filters['project_key'] = $projectKey;
            }

            // Note: Status and Priority filters are currently handled client-side in JS
            // But we could pass them here if we extended the Service. 
            // For now, let's stick to the plan where JS filters simpler attributes.

            if ($start && $end) {
                // Use the private getEvents method exposed via getDateRangeEvents-like logic?
                // The service has getDateRangeEvents($start, $end). 
                // Wait, I made getEvents private in Service but public wrappers don't take filters array.
                // I should probably make getEvents public or add a method that accepts filters.

                // Correction: I should update the Service to allow passing generic filters or adding methods.
                // But I've already written the Service. let's check it.
                // Service::getProjectDateRangeEvents($projectKey, $start, $end) calls getEvents with filter.

                if ($projectKey) {
                    $events = $this->calendarService->getProjectDateRangeEvents($projectKey, $start, $end);
                } else {
                    $events = $this->calendarService->getDateRangeEvents($start, $end);
                }
            } else {
                // Fallback to month/year if no range provided (legacy support)
                $year = (int) ($request->input('year') ?? date('Y'));
                $month = (int) ($request->input('month') ?? date('m'));

                if ($projectKey) {
                    $events = $this->calendarService->getProjectEvents($projectKey, $year, $month);
                } else {
                    $events = $this->calendarService->getMonthEvents($year, $month);
                }
            }

            $this->json(['success' => true, 'data' => $events]);

        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get Projects for Filter
     */
    public function projects(): void
    {
        if (!$this->authenticated()) {
            $this->redirect('/login');
        }
        try {
            $projects = $this->calendarService->getProjectsForFilter();
            $this->json(['success' => true, 'data' => $projects]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Upcoming Issues
     */
    public function upcoming(): void
    {
        if (!$this->authenticated()) {
            $this->redirect('/login');
        }
        try {
            $events = $this->calendarService->getUpcomingIssues();
            $this->json(['success' => true, 'data' => $events]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Overdue Issues
     */
    public function overdue(): void
    {
        if (!$this->authenticated()) {
            $this->redirect('/login');
        }
        try {
            $events = $this->calendarService->getOverdueIssues();
            $this->json(['success' => true, 'data' => $events]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get statuses for filter
     */
    public function statuses(): void
    {
        if (!$this->authenticated()) {
            $this->redirect('/login');
        }
        try {
            $statuses = $this->calendarService->getStatusesForFilter();
            $this->json(['success' => true, 'data' => $statuses]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get priorities for filter
     */
    public function priorities(): void
    {
        if (!$this->authenticated()) {
            $this->redirect('/login');
        }
        try {
            $priorities = $this->calendarService->getPrioritiesForFilter();
            $this->json(['success' => true, 'data' => $priorities]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get issue types for filter
     */
    public function issueTypes(): void
    {
        if (!$this->authenticated()) {
            $this->redirect('/login');
        }
        try {
            $types = $this->calendarService->getIssueTypesForFilter();
            $this->json(['success' => true, 'data' => $types]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get users for assignee filter
     */
    public function users(): void
    {
        if (!$this->authenticated()) {
            $this->redirect('/login');
        }
        try {
            $users = $this->calendarService->getUsersForFilter();
            $this->json(['success' => true, 'data' => $users]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Toggle Watch Status
     */
    public function toggleWatch(Request $request): void
    {
        if (!$this->authenticated()) {
            $this->redirect('/login');
        } // Assuming any viewer can watch
        $key = $request->param('key');
        $user = $request->user();

        if (!$key || !$user) {
            $this->json(['success' => false, 'error' => 'Invalid request'], 400);
            return;
        }

        try {
            $isWatching = $this->calendarService->toggleWatch($key, $user['id']);
            $this->json(['success' => true, 'isWatching' => $isWatching]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Check Watch Status
     */
    public function checkWatchStatus(Request $request): void
    {
        if (!$this->authenticated()) {
            $this->redirect('/login');
        }
        $key = $request->param('key');
        $user = $request->user();

        if (!$key || !$user) {
            $this->json(['success' => false, 'error' => 'Invalid request'], 400);
            return;
        }

        try {
            $isWatching = $this->calendarService->getWatchStatus($key, $user['id']);
            $this->json(['success' => true, 'isWatching' => $isWatching]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get Unscheduled Issues
     */
    public function unscheduled(): void
    {
        if (!$this->authenticated()) {
            $this->redirect('/login');
        }
        try {
            $issues = $this->calendarService->getUnscheduledIssues();
            $this->json(['success' => true, 'data' => $issues]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Schedule Unscheduled Issue
     */
    public function scheduleIssue(Request $request): void
    {
        $this->authorize('issues.edit');

        $issueId = $request->input('issue_id');
        $dueDate = $request->input('due_date');
        $startDate = $request->input('start_date');

        if (!$issueId || !$dueDate) {
            $this->json(['success' => false, 'error' => 'Issue ID and due date are required'], 400);
            return;
        }

        try {
            $updated = \App\Core\Database::update(
                'issues',
                [
                    'due_date' => $dueDate,
                    'start_date' => $startDate ?: null,
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                'id = ?',
                [$issueId]
            );

            if ($updated) {
                $this->json(['success' => true, 'message' => 'Issue scheduled successfully']);
            } else {
                $this->json(['success' => false, 'error' => 'Failed to schedule issue'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
