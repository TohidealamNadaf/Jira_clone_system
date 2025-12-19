<?php
/**
 * Calendar Controller - Handles calendar view requests and events
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
     * Display global calendar view
     */
    public function index(Request $request): string
    {
        $this->authorize('issues.view');
        
        // Get current month/year from request or use current date
        $year = (int) ($request->input('year') ?? date('Y'));
        $month = (int) ($request->input('month') ?? date('m'));
        
        return $this->view('calendar.index', [
            'year' => $year,
            'month' => $month,
        ]);
    }
    
    /**
     * Display calendar view for a specific project
     */
    public function show(Request $request): string
    {
        $projectKey = $request->getParameter('key');
        
        $this->authorize('issues.view');
        
        $year = (int) ($request->input('year') ?? date('Y'));
        $month = (int) ($request->input('month') ?? date('m'));
        
        return $this->view('projects.calendar', [
            'projectKey' => $projectKey,
            'year' => $year,
            'month' => $month,
        ]);
    }
    
    /**
     * API endpoint: Get calendar events
     * Supports both month/year and date range queries
     */
    public function getEvents(Request $request): void
    {
        $this->authorize('issues.view');
        
        try {
            $projectKey = $request->input('project');
            
            // Get month/year or date range from request
            if ($request->input('start') && $request->input('end')) {
                // Date range request (typically from FullCalendar)
                $start = $request->input('start');
                $end = $request->input('end');
                
                if ($projectKey) {
                    $events = $this->calendarService->getProjectDateRangeEvents($projectKey, $start, $end);
                } else {
                    $events = $this->calendarService->getDateRangeEvents($start, $end);
                }
            } else {
                // Month/year request
                $year = (int) ($request->input('year') ?? date('Y'));
                $month = (int) ($request->input('month') ?? date('m'));
                
                if ($projectKey) {
                    $events = $this->calendarService->getProjectEvents($projectKey, $year, $month);
                } else {
                    $events = $this->calendarService->getMonthEvents($year, $month);
                }
            }
            
            // Return with status code 200
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $events]);
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
    
    /**
     * API endpoint: Get upcoming issues (next 30 days by default)
     */
    public function upcoming(Request $request): void
    {
        $this->authorize('issues.view');
        
        try {
            $events = $this->calendarService->getUpcomingIssues();
            
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $events]);
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
    
    /**
     * API endpoint: Get overdue issues
     */
    public function overdue(Request $request): void
    {
        $this->authorize('issues.view');
        
        try {
            $events = $this->calendarService->getOverdueIssues();
            
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $events]);
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
    
    /**
     * API endpoint: Get projects for dropdown filter
     */
    public function projects(Request $request): void
    {
        $this->authorize('issues.view');
        
        try {
            $projects = $this->calendarService->getProjectsForFilter();
            
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $projects]);
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
}
