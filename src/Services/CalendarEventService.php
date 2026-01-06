<?php
/**
 * Calendar Event Service
 * Manages calendar events separate from issues
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class CalendarEventService
{
    /**
     * Get available event types
     */
    public function getEventTypes(): array
    {
        return [
            ['value' => 'issue', 'label' => 'Issue Due Date', 'icon' => 'bi-bug', 'color' => '#FF5630'],
            ['value' => 'sprint', 'label' => 'Sprint Start/End', 'icon' => 'bi-flag', 'color' => '#0052CC'],
            ['value' => 'milestone', 'label' => 'Milestone', 'icon' => 'bi-star', 'color' => '#6554C0'],
            ['value' => 'reminder', 'label' => 'Reminder', 'icon' => 'bi-bell', 'color' => '#FFAB00'],
            ['value' => 'meeting', 'label' => 'Meeting', 'icon' => 'bi-people', 'color' => '#36B37E']
        ];
    }

    /**
     * Get priorities from database
     */
    public function getPriorities(): array
    {
        try {
            $sql = "
                SELECT id, name, color, icon, sort_order 
                FROM issue_priorities 
                ORDER BY sort_order ASC, name ASC
            ";
            
            return Database::select($sql);
        } catch (\Exception $e) {
            // Fallback to default priorities if database query fails
            return [
                ['id' => 1, 'name' => 'Low', 'color' => '#6554C0', 'icon' => 'low', 'sort_order' => 1],
                ['id' => 2, 'name' => 'Medium', 'color' => '#FFAB00', 'icon' => 'medium', 'sort_order' => 2],
                ['id' => 3, 'name' => 'High', 'color' => '#DE350B', 'icon' => 'high', 'sort_order' => 3],
                ['id' => 4, 'name' => 'Urgent', 'color' => '#FF5630', 'icon' => 'urgent', 'sort_order' => 4]
            ];
        }
    }

    /**
     * Get active users for attendees
     */
    public function getActiveUsers(): array
    {
        try {
            $sql = "
                SELECT id, CONCAT(first_name, ' ', last_name) as name, email, avatar
                FROM users 
                WHERE is_active = 1 
                ORDER BY first_name ASC, last_name ASC
            ";
            
            $users = Database::select($sql);
            
            // Add avatar initials if no avatar
            foreach ($users as &$user) {
                if (empty($user['avatar'])) {
                    $names = explode(' ', $user['name'] ?? '');
                    $user['initials'] = '';
                    foreach ($names as $name) {
                        if (!empty($name)) {
                            $user['initials'] .= strtoupper(substr($name, 0, 1));
                        }
                    }
                }
            }
            
            return $users;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get projects for dropdown
     */
    public function getProjects(): array
    {
        try {
            $sql = "
                SELECT id, key, name 
                FROM projects 
                WHERE is_archived = 0 
                ORDER BY name ASC
            ";
            
            return Database::select($sql);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Create new calendar event
     */
    public function createEvent(array $data): array
    {
        $sql = "
            INSERT INTO calendar_events (
                event_type, project_id, title, description, start_date, end_date,
                priority_id, attendees, reminders, recurring_type, recurring_interval,
                recurring_ends, recurring_end_date, created_by, created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
            )
        ";
        
        $params = [
            $data['event_type'],
            $data['project_id'] ?? null,
            $data['title'],
            $data['description'] ?? null,
            $data['start_date'],
            $data['end_date'],
            $this->getPriorityIdByName($data['priority']),
            $data['attendees'] ?? null,
            json_encode($data['reminders'] ?? []),
            $data['recurring_type'] ?? 'none',
            $data['recurring_interval'] ?? null,
            $data['recurring_ends'] ?? null,
            $data['recurring_end_date'] ?? null,
            \App\Core\Session::get('user_id')
        ];
        
        Database::insert($sql, $params);
        
        $eventId = (int) Database::selectValue("SELECT LAST_INSERT_ID()");
        
        return $this->getEventById($eventId);
    }

    /**
     * Update calendar event
     */
    public function updateEvent(int $eventId, array $data): array
    {
        $sql = "
            UPDATE calendar_events SET
                event_type = ?, project_id = ?, title = ?, description = ?, start_date = ?, end_date = ?,
                priority_id = ?, attendees = ?, reminders = ?, recurring_type = ?, recurring_interval = ?,
                recurring_ends = ?, recurring_end_date = ?, updated_at = NOW()
            WHERE id = ? AND created_by = ?
        ";
        
        $params = [
            $data['event_type'],
            $data['project_id'] ?? null,
            $data['title'],
            $data['description'] ?? null,
            $data['start_date'],
            $data['end_date'],
            $this->getPriorityIdByName($data['priority']),
            $data['attendees'] ?? null,
            json_encode($data['reminders'] ?? []),
            $data['recurring_type'] ?? 'none',
            $data['recurring_interval'] ?? null,
            $data['recurring_ends'] ?? null,
            $data['recurring_end_date'] ?? null,
            $eventId,
            \App\Core\Session::get('user_id')
        ];
        
        Database::update('calendar_events', $data, "id = ? AND created_by = ?", [$eventId, \App\Core\Session::get('user_id')]);
        
        return $this->getEventById($eventId);
    }

    /**
     * Delete calendar event
     */
    public function deleteEvent(int $eventId): bool
    {
        $sql = "DELETE FROM calendar_events WHERE id = ? AND created_by = ?";
        Database::delete('calendar_events', "id = ? AND created_by = ?", [$eventId, \App\Core\Session::get('user_id')]);
        return true;
    }

    /**
     * Get events with filters
     */
    public function getEvents(array $filters = []): array
    {
        $sql = "
            SELECT ce.*, 
                   p.key as project_key, p.name as project_name,
                   ip.name as priority_name, ip.color as priority_color,
                   u.first_name as creator_first_name, u.last_name as creator_last_name
            FROM calendar_events ce
            LEFT JOIN projects p ON ce.project_id = p.id
            LEFT JOIN issue_priorities ip ON ce.priority_id = ip.id
            LEFT JOIN users u ON ce.created_by = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND ce.end_date >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND ce.start_date <= ?";
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['event_type'])) {
            $sql .= " AND ce.event_type = ?";
            $params[] = $filters['event_type'];
        }
        
        if (!empty($filters['project_id'])) {
            $sql .= " AND ce.project_id = ?";
            $params[] = $filters['project_id'];
        }
        
        if (!empty($filters['priority'])) {
            $priorityId = $this->getPriorityIdByName($filters['priority']);
            $sql .= " AND ce.priority_id = ?";
            $params[] = $priorityId;
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (ce.title LIKE ? OR ce.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY ce.start_date ASC";
        
        return Database::select($sql);
    }

    /**
     * Get single event by ID
     */
    public function getEventById(int $eventId): array
    {
        $sql = "
            SELECT ce.*, 
                   p.key as project_key, p.name as project_name,
                   ip.name as priority_name, ip.color as priority_color,
                   u.first_name as creator_first_name, u.last_name as creator_last_name
            FROM calendar_events ce
            LEFT JOIN projects p ON ce.project_id = p.id
            LEFT JOIN issue_priorities ip ON ce.priority_id = ip.id
            LEFT JOIN users u ON ce.created_by = u.id
            WHERE ce.id = ? AND ce.created_by = ?
        ";
        
        $event = Database::selectOne($sql, [$eventId, \App\Core\Session::get('user_id')]);
        return $event ?? [];
    }

    /**
     * Get priority ID by name (from database or fallback)
     */
    private function getPriorityIdByName(string $name): int
    {
        static $priorityMap = null;
        
        if ($priorityMap === null) {
            $priorities = $this->getPriorities();
            foreach ($priorities as $priority) {
                $priorityMap[strtolower($priority['name'])] = (int) $priority['id'];
            }
            
            // Fallback mapping
            $priorityMap = array_merge($priorityMap, [
                'low' => 1,
                'medium' => 2, 
                'high' => 3,
                'urgent' => 4
            ]);
        }
        
        return $priorityMap[strtolower($name)] ?? 2; // Default to medium
    }
}