<?php
/**
 * Calendar Event Controller
 * Handles creation and management of calendar events
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\CalendarEventService;

class CalendarEventController extends Controller
{
    private CalendarEventService $calendarEventService;

    public function __construct()
    {
        $this->calendarEventService = new CalendarEventService();
    }

    /**
     * API: Get event types
     */
    public function getEventTypes(): void
    {
        $this->authorize('issues.view');
        
        try {
            $eventTypes = $this->calendarEventService->getEventTypes();
            $this->json(['success' => true, 'data' => $eventTypes]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get priorities
     */
    public function getPriorities(): void
    {
        $this->authorize('issues.view');
        
        try {
            $priorities = $this->calendarEventService->getPriorities();
            $this->json(['success' => true, 'data' => $priorities]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get users for attendees
     */
    public function getUsers(): void
    {
        $this->authorize('issues.view');
        
        try {
            $users = $this->calendarEventService->getActiveUsers();
            $this->json(['success' => true, 'data' => $users]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Create calendar event
     */
    public function create(Request $request): void
    {
        $this->authorize('issues.create');
        
        try {
            $data = $request->validate([
                'event_type' => 'required|string|in:issue,sprint,milestone,reminder,meeting',
                'project_id' => 'nullable|integer',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'priority' => 'required|string',
                'attendees' => 'nullable|string',
                'reminders' => 'nullable|array',
                'recurring_type' => 'nullable|string|in:none,daily,weekly,monthly,yearly,custom',
                'recurring_interval' => 'nullable|integer|min:1|max:99',
                'recurring_ends' => 'nullable|string|in:never,after,on',
                'recurring_end_date' => 'nullable|date'
            ]);

            $event = $this->calendarEventService->createEvent($data);
            
            $this->json([
                'success' => true, 
                'data' => $event,
                'message' => 'Calendar event created successfully'
            ]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get calendar events
     */
    public function getEvents(Request $request): void
    {
        $this->authorize('issues.view');
        
        try {
            $filters = [
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'event_type' => $request->input('event_type'),
                'project_id' => $request->input('project_id'),
                'priority' => $request->input('priority'),
                'search' => $request->input('search')
            ];

            $events = $this->calendarEventService->getEvents($filters);
            
            $this->json(['success' => true, 'data' => $events]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Update calendar event
     */
    public function update(Request $request): void
    {
        $this->authorize('issues.edit');
        
        try {
            $eventId = (int) $request->param('id');
            $data = $request->validate([
                'event_type' => 'required|string|in:issue,sprint,milestone,reminder,meeting',
                'project_id' => 'nullable|integer',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'priority' => 'required|string',
                'attendees' => 'nullable|string',
                'reminders' => 'nullable|array',
                'recurring_type' => 'nullable|string|in:none,daily,weekly,monthly,yearly,custom',
                'recurring_interval' => 'nullable|integer|min:1|max:99',
                'recurring_ends' => 'nullable|string|in:never,after,on',
                'recurring_end_date' => 'nullable|date'
            ]);

            $event = $this->calendarEventService->updateEvent($eventId, $data);
            
            $this->json([
                'success' => true, 
                'data' => $event,
                'message' => 'Calendar event updated successfully'
            ]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Delete calendar event
     */
    public function delete(Request $request): void
    {
        $this->authorize('issues.delete');
        
        try {
            $eventId = (int) $request->param('id');
            $this->calendarEventService->deleteEvent($eventId);
            
            $this->json([
                'success' => true, 
                'message' => 'Calendar event deleted successfully'
            ]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}