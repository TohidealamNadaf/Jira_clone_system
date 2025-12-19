<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use Exception;

/**
 * Support Ticket Service
 * 
 * Manages support ticket operations including creation, assignment, SLA tracking,
 * and ticket lifecycle management.
 */
class SupportTicketService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create a new support ticket
     *
     * @param array $data Ticket data (subject, description, priority, category, assigned_to_user_id)
     * @return array Created ticket with ID and ticket_number
     * @throws Exception
     */
    public function create(array $data): array
    {
        try {
            // Generate ticket number
            $ticketNumber = $this->generateTicketNumber();

            // Calculate SLA due dates
            $slaDates = $this->calculateSLADates($data['priority'] ?? 'medium', $data['category'] ?? null);

            // Prepare insertion data
            $insertData = [
                'ticket_number' => $ticketNumber,
                'subject' => $data['subject'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? 'medium',
                'category' => $data['category'] ?? null,
                'assigned_to_user_id' => $data['assigned_to_user_id'] ?? null,
                'status' => 'open',
                'sla_due_date' => $slaDates['resolution_due'],
                'first_response_sla_due' => $slaDates['first_response_due'],
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // Insert ticket
            $result = $this->db->insert('support_tickets', $insertData);

            if (!$result) {
                throw new Exception('Failed to create support ticket');
            }

            // Get the created ticket
            $ticket = $this->getById($this->db->lastInsertId());

            // Update agent's current ticket count if assigned
            if ($data['assigned_to_user_id'] ?? null) {
                $this->updateAgentWorkload($data['assigned_to_user_id'], 1);
            }

            return $ticket;
        } catch (Exception $e) {
            throw new Exception("Failed to create support ticket: {$e->getMessage()}");
        }
    }

    /**
     * Get all support tickets with filters
     *
     * @param array $filters status, priority, assigned_to, category
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        try {
            $query = 'SELECT * FROM support_tickets WHERE 1=1';
            $params = [];

            // Apply filters
            if (!empty($filters['status'])) {
                $query .= ' AND status = ?';
                $params[] = $filters['status'];
            }

            if (!empty($filters['priority'])) {
                $query .= ' AND priority = ?';
                $params[] = $filters['priority'];
            }

            if (!empty($filters['assigned_to'])) {
                $query .= ' AND assigned_to_user_id = ?';
                $params[] = $filters['assigned_to'];
            }

            if (!empty($filters['category'])) {
                $query .= ' AND category = ?';
                $params[] = $filters['category'];
            }

            $query .= ' ORDER BY created_at DESC LIMIT ? OFFSET ?';
            $params[] = $limit;
            $params[] = $offset;

            return $this->db->select($query, $params);
        } catch (Exception $e) {
            throw new Exception("Failed to fetch support tickets: {$e->getMessage()}");
        }
    }

    /**
     * Get a single ticket by ID
     *
     * @param int $id
     * @return array
     * @throws Exception
     */
    public function getById(int $id): array
    {
        try {
            $result = $this->db->select(
                'SELECT * FROM support_tickets WHERE id = ?',
                [$id]
            );

            if (empty($result)) {
                throw new Exception("Ticket not found with ID: {$id}");
            }

            return $result[0];
        } catch (Exception $e) {
            throw new Exception("Failed to fetch ticket: {$e->getMessage()}");
        }
    }

    /**
     * Update a support ticket
     *
     * @param int $id
     * @param array $data
     * @return array Updated ticket
     * @throws Exception
     */
    public function update(int $id, array $data): array
    {
        try {
            $ticket = $this->getById($id);

            // If status changed, update resolution time
            if (isset($data['status']) && $data['status'] !== $ticket['status']) {
                if ($data['status'] === 'resolved' || $data['status'] === 'closed') {
                    $data['resolved_at'] = date('Y-m-d H:i:s');

                    // Calculate resolution time
                    $createdTime = strtotime($ticket['created_at']);
                    $resolvedTime = strtotime($data['resolved_at']);
                    $data['resolution_time'] = intval(($resolvedTime - $createdTime) / 60);
                }
            }

            // If assignment changed, update workload
            if (isset($data['assigned_to_user_id']) && $data['assigned_to_user_id'] !== $ticket['assigned_to_user_id']) {
                // Reduce previous agent's workload
                if ($ticket['assigned_to_user_id']) {
                    $this->updateAgentWorkload($ticket['assigned_to_user_id'], -1);
                }
                // Increase new agent's workload
                if ($data['assigned_to_user_id']) {
                    $this->updateAgentWorkload($data['assigned_to_user_id'], 1);
                }
            }

            $data['updated_at'] = date('Y-m-d H:i:s');

            $updated = $this->db->update(
                'support_tickets',
                $data,
                'id = ?',
                [$id]
            );

            if (!$updated) {
                throw new Exception('Failed to update support ticket');
            }

            return $this->getById($id);
        } catch (Exception $e) {
            throw new Exception("Failed to update ticket: {$e->getMessage()}");
        }
    }

    /**
     * Delete a support ticket
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        try {
            $ticket = $this->getById($id);

            // Update agent workload
            if ($ticket['assigned_to_user_id']) {
                $this->updateAgentWorkload($ticket['assigned_to_user_id'], -1);
            }

            // Delete interactions first (foreign key)
            $this->db->delete('ticket_interactions', 'ticket_id = ?', [$id]);

            // Delete customer feedback
            $this->db->delete('customer_feedback', 'ticket_id = ?', [$id]);

            // Delete ticket
            return $this->db->delete('support_tickets', 'id = ?', [$id]);
        } catch (Exception $e) {
            throw new Exception("Failed to delete ticket: {$e->getMessage()}");
        }
    }

    /**
     * Get dashboard statistics
     *
     * @return array
     */
    public function getDashboardStats(): array
    {
        try {
            $stats = [
                'total_open' => 0,
                'avg_resolution_time' => 0,
                'customer_satisfaction' => 0,
                'sla_breaches' => 0,
                'by_priority' => [],
                'by_agent' => [],
                'recent_interactions' => [],
            ];

            // Total open tickets
            $result = $this->db->select(
                'SELECT COUNT(*) as count FROM support_tickets WHERE status = "open"',
                []
            );
            $stats['total_open'] = $result[0]['count'] ?? 0;

            // Average resolution time (for closed tickets)
            $result = $this->db->select(
                'SELECT AVG(resolution_time) as avg_time FROM support_tickets WHERE status = "closed" AND resolution_time IS NOT NULL',
                []
            );
            $stats['avg_resolution_time'] = round($result[0]['avg_time'] ?? 0);

            // Average customer satisfaction
            $result = $this->db->select(
                'SELECT AVG(rating) as avg_rating FROM customer_feedback',
                []
            );
            $stats['customer_satisfaction'] = round($result[0]['avg_rating'] ?? 0, 1);

            // SLA breaches (past due and still open)
            $result = $this->db->select(
                'SELECT COUNT(*) as count FROM support_tickets 
                 WHERE sla_due_date < NOW() AND status NOT IN ("resolved", "closed")',
                []
            );
            $stats['sla_breaches'] = $result[0]['count'] ?? 0;

            // Tickets by priority
            $result = $this->db->select(
                'SELECT priority, COUNT(*) as count FROM support_tickets 
                 WHERE status NOT IN ("resolved", "closed")
                 GROUP BY priority',
                []
            );
            foreach ($result as $row) {
                $stats['by_priority'][$row['priority']] = $row['count'];
            }

            // Workload by agent
            $result = $this->db->select(
                'SELECT u.id, u.name, sta.current_tickets, sta.max_tickets
                 FROM support_team_assignments sta
                 JOIN users u ON sta.user_id = u.id
                 ORDER BY sta.current_tickets DESC',
                []
            );
            $stats['by_agent'] = $result;

            // Recent interactions (last 10)
            $result = $this->db->select(
                'SELECT ti.*, st.ticket_number, u.name 
                 FROM ticket_interactions ti
                 JOIN support_tickets st ON ti.ticket_id = st.id
                 JOIN users u ON ti.user_id = u.id
                 ORDER BY ti.created_at DESC LIMIT 10',
                []
            );
            $stats['recent_interactions'] = $result;

            return $stats;
        } catch (Exception $e) {
            return []; // Return empty stats on error
        }
    }

    /**
     * Auto-assign ticket to available agent
     *
     * @param int $ticketId
     * @return bool
     */
    public function autoAssign(int $ticketId): bool
    {
        try {
            // Find available agent with lowest ticket count
            $result = $this->db->select(
                'SELECT user_id FROM support_team_assignments 
                 WHERE availability_status = "available" 
                 AND current_tickets < max_tickets
                 ORDER BY current_tickets ASC LIMIT 1',
                []
            );

            if (empty($result)) {
                return false; // No available agents
            }

            $assignedUserId = $result[0]['user_id'];

            // Update ticket assignment
            $this->update($ticketId, ['assigned_to_user_id' => $assignedUserId]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Generate unique ticket number (SUP-XXXX)
     *
     * @return string
     */
    private function generateTicketNumber(): string
    {
        try {
            $result = $this->db->select(
                'SELECT COALESCE(MAX(CAST(SUBSTRING(ticket_number, 5) AS UNSIGNED)), 0) + 1 as next_number
                 FROM support_tickets WHERE ticket_number LIKE "SUP-%"',
                []
            );

            $nextNumber = $result[0]['next_number'] ?? 1;
            return 'SUP-' . str_pad((string)$nextNumber, 4, '0', STR_PAD_LEFT);
        } catch (Exception $e) {
            // Fallback: use timestamp
            return 'SUP-' . time();
        }
    }

    /**
     * Calculate SLA due dates based on priority
     *
     * @param string $priority
     * @param string|null $category
     * @return array
     */
    private function calculateSLADates(string $priority, ?string $category): array
    {
        // Default SLA times (in minutes)
        $slaMatrix = [
            'urgent' => ['first_response' => 15, 'resolution' => 240],
            'high' => ['first_response' => 60, 'resolution' => 1440],
            'medium' => ['first_response' => 120, 'resolution' => 2880],
            'low' => ['first_response' => 480, 'resolution' => 5760],
        ];

        $sla = $slaMatrix[$priority] ?? $slaMatrix['medium'];

        $now = new \DateTime();
        $firstResponseDue = clone $now;
        $firstResponseDue->modify("+{$sla['first_response']} minutes");

        $resolutionDue = clone $now;
        $resolutionDue->modify("+{$sla['resolution']} minutes");

        return [
            'first_response_due' => $firstResponseDue->format('Y-m-d H:i:s'),
            'resolution_due' => $resolutionDue->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Update agent's current ticket count
     *
     * @param int $userId
     * @param int $delta
     * @return void
     */
    private function updateAgentWorkload(int $userId, int $delta): void
    {
        try {
            $this->db->query(
                'UPDATE support_team_assignments SET current_tickets = current_tickets + ? WHERE user_id = ?',
                [$delta, $userId]
            );
        } catch (Exception $e) {
            // Log error but don't fail the main operation
        }
    }
}
