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
     * Display the Calendar View
     */
    public function index(Request $request): string
    {
        $this->authorize('issues.view');
        return $this->view('calendar.index');
    }

    /**
     * Display Project Calendar Routes
     */
    public function show(Request $request): string
    {
        $this->authorize('issues.view');
        // We can reuse the main view but pass the project key to pre-filter in JS if needed
        return $this->view('calendar.index', ['projectKey' => $request->param('key')]);
    }

    /**
     * API: Get Events
     */
    public function getEvents(Request $request): void
    {
        $this->authorize('issues.view');

        try {
            // FullCalendar sends 'start' and 'end' as ISO strings
            $start = $request->input('start');
            $end = $request->input('end');

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
    public function projects(Request $request): void
    {
        $this->authorize('issues.view');
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
    public function upcoming(Request $request): void
    {
        $this->authorize('issues.view');
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
    public function overdue(Request $request): void
    {
        $this->authorize('issues.view');
        try {
            $events = $this->calendarService->getOverdueIssues();
            $this->json(['success' => true, 'data' => $events]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }


}
